<?php

namespace App\Services;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class SalesService
{
    public function __construct(
        private StockService $stockService
    ) {}

    /**
     * List all penjualan with pagination.
     */
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Penjualan::with(['pelanggan', 'admin', 'lokasi', 'detail.produk'])
            ->when($search, function ($query) use ($search) {
                $query->where('nomor_nota', 'like', "%{$search}%")
                      ->orWhereHas('pelanggan', fn($q) => $q->where('nama_pelanggan', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a penjualan by ID.
     */
    public function find(int $id): Penjualan
    {
        return Penjualan::with(['pelanggan', 'admin', 'lokasi', 'detail.produk'])->findOrFail($id);
    }

    /**
     * Generate Nomor Nota (INV-YYYYMMDD-XXXX)
     */
    public function generateNomorNota(): string
    {
        $prefix = 'INV';
        $dateStr = now()->format('Ymd');
        
        $lastNota = Penjualan::where('nomor_nota', 'like', "{$prefix}-{$dateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastNota) {
            $nextNumber = '0001';
        } else {
            $lastNumber = explode('-', $lastNota->nomor_nota);
            $nextNumber = str_pad((int) end($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        }

        return "{$prefix}-{$dateStr}-{$nextNumber}";
    }

    /**
     * Create a penjualan with details.
     * Validates stock, reduces stock, and saves HPP snapshot.
     * Uses single loop to avoid double querying produk.
     */
    public function create(array $data, array $details): Penjualan
    {
        return DB::transaction(function () use ($data, $details) {
            // Pre-load all needed products in one query
            $produkIds = array_column($details, 'produk_id');
            $produkMap = Produk::whereIn('id', $produkIds)->get()->keyBy('id');

            // Validate stock availability and prepare details
            foreach ($details as &$detail) {
                $produk = $produkMap->get($detail['produk_id']);

                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$detail['produk_id']} tidak ditemukan.");
                }

                $available = $this->stockService->getStok($detail['produk_id'], $data['lokasi_id']);

                if ($available < $detail['qty_keluar']) {
                    $totalAvailable = $this->stockService->getTotalStok($detail['produk_id']);
                    $lokasiNama = \App\Models\Lokasi::find($data['lokasi_id'])->nama_lokasi ?? 'Terpilih';
                    
                    throw new \Exception(
                        "Stok tidak cukup untuk {$produk->nama_barang} di lokasi {$lokasiNama}. Tersedia: {$available} (Total Semua Lokasi: {$totalAvailable}), dibutuhkan: {$detail['qty_keluar']}"
                    );
                }

                // Attach HPP snapshot here so we don't need a second loop
                $detail['hpp_snapshot'] = $produk->hpp;
            }
            unset($detail);

            $penjualan = Penjualan::create($data);

            foreach ($details as $detail) {
                $penjualan->detail()->create($detail);

                // Decrease stock
                $this->stockService->decreaseStok(
                    $detail['produk_id'],
                    $data['lokasi_id'],
                    $detail['qty_keluar']
                );
            }

            \App\Services\ActivityLogService::log('create', $penjualan, "Mencatat penjualan (Nota: {$penjualan->nomor_nota})");

            return $penjualan->load('detail.produk');
        });
    }

    /**
     * Void a penjualan — restore stock and soft-delete the record.
     */
    public function void(Penjualan $penjualan): void
    {
        if ($penjualan->trashed()) {
            throw new \Exception('Transaksi ini sudah di-void sebelumnya.');
        }

        DB::transaction(function () use ($penjualan) {
            $penjualan->load('detail');

            // Restore stock for each detail
            foreach ($penjualan->detail as $detail) {
                $this->stockService->increaseStok(
                    $detail->produk_id,
                    $penjualan->lokasi_id,
                    $detail->qty_keluar
                );
            }

            // Mark as void
            $penjualan->update(['keterangan' => '[VOID] ' . ($penjualan->keterangan ?? '')]);
            $penjualan->delete(); // soft delete
        });

        \App\Services\ActivityLogService::log('void', $penjualan, "Void penjualan (Nota: {$penjualan->nomor_nota})");
    }

    /**
     * Get monthly data for chart (barang keluar).
     */
    public function getMonthlyData(int $year): array
    {
        return PenjualanDetail::whereHas('penjualan', function ($q) use ($year) {
            $q->whereYear('tanggal', $year);
        })
        ->selectRaw('MONTH(penjualan.tanggal) as bulan, SUM(qty_keluar) as total')
        ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
        ->groupByRaw('MONTH(penjualan.tanggal)')
        ->pluck('total', 'bulan')
        ->toArray();
    }
}
