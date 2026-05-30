<?php

namespace App\Services;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\PurchaseOrderDetail;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class GoodsReceiptService
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * List all barang masuk with pagination.
     */
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return BarangMasuk::with(['supplier', 'admin', 'lokasi', 'detail.produk'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                      ->orWhere('tanggal', 'like', "%{$search}%")
                      ->orWhereHas('supplier', fn($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"))
                      ->orWhereHas('detail.produk', fn($sq) => $sq->where('nama_barang', 'like', "%{$search}%")->orWhere('kode_barang', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a barang masuk by ID.
     */
    public function find(int $id): BarangMasuk
    {
        return BarangMasuk::with(['supplier', 'admin', 'lokasi', 'detail.produk'])->findOrFail($id);
    }

    /**
     * Generate Nomor Bukti Penerimaan (BM-YYYYMMDD-XXXX)
     */
    public function generateNomorNota(): string
    {
        $prefix = 'BM';
        $dateStr = now()->format('Ymd');
        
        $lastBm = BarangMasuk::where('nomor_nota', 'like', "{$prefix}-{$dateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBm) {
            $nextNumber = '0001';
        } else {
            $lastNumber = explode('-', $lastBm->nomor_nota);
            $nextNumber = str_pad((int) end($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        }

        return "{$prefix}-{$dateStr}-{$nextNumber}";
    }

    /**
     * Create barang masuk with details.
     * Updates stok, updates PO sisa, and updates HPP.
     */
    public function create(array $data, array $details): BarangMasuk
    {
        return DB::transaction(function () use ($data, $details) {
            $barangMasuk = BarangMasuk::create($data);

            foreach ($details as $detail) {
                // Create detail record
                $barangMasuk->detail()->create($detail);

                // Update stok
                $this->stockService->increaseStok(
                    $detail['produk_id'],
                    $data['lokasi_id'],
                    $detail['qty_masuk']
                );

                // Update HPP (weighted average)
                $this->updateHpp($detail['produk_id'], $detail['qty_masuk'], $detail['harga_beli']);

                // Update PO sisa if linked
                $this->updatePoSisa($detail['produk_id'], $detail['qty_masuk']);
            }

            \App\Services\ActivityLogService::log('create', $barangMasuk, "Mencatat barang masuk dari {$barangMasuk->supplier->nama_supplier}");

            return $barangMasuk->load('detail.produk');
        });
    }

    /**
     * Void a barang masuk — decrease stock, reverse PO sisa, soft-delete record.
     */
    public function void(BarangMasuk $barangMasuk): void
    {
        if ($barangMasuk->trashed()) {
            throw new \Exception('Transaksi ini sudah di-void sebelumnya.');
        }

        DB::transaction(function () use ($barangMasuk) {
            $barangMasuk->load('detail');

            foreach ($barangMasuk->detail as $detail) {
                // Decrease stock
                $this->stockService->decreaseStok(
                    $detail->produk_id,
                    $barangMasuk->lokasi_id,
                    $detail->qty_masuk
                );

                // Reverse PO sisa
                $this->reversePoSisa($detail->produk_id, $detail->qty_masuk);
            }

            // Mark as void
            $barangMasuk->update(['keterangan' => '[VOID] ' . ($barangMasuk->keterangan ?? '')]);
            $barangMasuk->delete(); // soft delete
        });

        \App\Services\ActivityLogService::log('void', $barangMasuk, "Void barang masuk dari {$barangMasuk->supplier->nama_supplier}");
    }

    /**
     * Reverse PO sisa when voiding a barang masuk.
     */
    private function reversePoSisa(int $produkId, int $qtyMasuk): void
    {
        $poDetails = PurchaseOrderDetail::where('produk_id', $produkId)
            ->where('barang_masuk', '>', 0)
            ->orderByDesc('id')
            ->get();

        $remaining = $qtyMasuk;

        foreach ($poDetails as $poDetail) {
            /** @var PurchaseOrderDetail $poDetail */
            if ($remaining <= 0) break;

            $restore = min($remaining, $poDetail->barang_masuk);
            $poDetail->barang_masuk -= $restore;
            $poDetail->sisa += $restore;
            $poDetail->save();

            $remaining -= $restore;

            // Update PO status
            $po = $poDetail->purchaseOrder;
            $totalSisa = $po->detail()->sum('sisa');
            $totalJumlah = $po->detail()->sum('jumlah');
            if ($totalSisa === $totalJumlah) {
                $po->update(['status' => 'draft']);
            } else {
                $po->update(['status' => 'sebagian']);
            }
        }
    }

    /**
     * Update HPP using weighted average method.
     */
    private function updateHpp(int $produkId, int $qtyBaru, float $hargaBeli): void
    {
        $produk = Produk::findOrFail($produkId);
        $totalStokLama = $this->stockService->getTotalStok($produkId) - $qtyBaru;
        $nilaiLama = $produk->hpp * $totalStokLama;
        $nilaiBaru = $hargaBeli * $qtyBaru;
        $totalStokBaru = $totalStokLama + $qtyBaru;

        if ($totalStokBaru > 0) {
            $produk->hpp = ($nilaiLama + $nilaiBaru) / $totalStokBaru;
            $produk->save();
        }
    }

    /**
     * Update PO sisa when barang masuk received.
     */
    private function updatePoSisa(int $produkId, int $qtyMasuk): void
    {
        $poDetails = PurchaseOrderDetail::where('produk_id', $produkId)
            ->where('sisa', '>', 0)
            ->orderBy('id')
            ->get();

        $remaining = $qtyMasuk;

        foreach ($poDetails as $poDetail) {
            /** @var PurchaseOrderDetail $poDetail */
            if ($remaining <= 0) break;

            $reduce = min($remaining, $poDetail->sisa);
            $poDetail->barang_masuk += $reduce;
            $poDetail->sisa -= $reduce;
            $poDetail->save();

            $remaining -= $reduce;

            // Update PO status
            $po = $poDetail->purchaseOrder;
            $totalSisa = $po->detail()->sum('sisa');
            if ($totalSisa === 0) {
                $po->update(['status' => 'selesai']);
            } else {
                $po->update(['status' => 'sebagian']);
            }
        }
    }

    /**
     * Get monthly data for chart (barang masuk).
     */
    public function getMonthlyData(int $year): array
    {
        return BarangMasukDetail::whereHas('barangMasuk', function ($q) use ($year) {
            $q->whereYear('tanggal', $year);
        })
        ->selectRaw('MONTH(barang_masuk.tanggal) as bulan, SUM(qty_masuk) as total')
        ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
        ->groupByRaw('MONTH(barang_masuk.tanggal)')
        ->pluck('total', 'bulan')
        ->toArray();
    }
}
