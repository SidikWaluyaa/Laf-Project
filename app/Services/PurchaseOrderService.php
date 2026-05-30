<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderService
{
    /**
     * List all PO with pagination.
     */
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return PurchaseOrder::with(['supplier', 'detail.produk'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhere('tanggal', 'like', "%{$search}%")
                      ->orWhereHas('supplier', fn($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"))
                      ->orWhereHas('detail.produk', fn($sq) => $sq->where('nama_barang', 'like', "%{$search}%")->orWhere('kode_barang', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a PO by ID.
     */
    public function find(int $id): PurchaseOrder
    {
        return PurchaseOrder::with(['supplier', 'detail.produk'])->findOrFail($id);
    }

    /**
     * Generate Nomor PO (PO-YYYYMMDD-XXXX)
     */
    public function generateNomorPo(): string
    {
        $prefix = 'PO';
        $dateStr = now()->format('Ymd');
        
        $lastPo = PurchaseOrder::where('nomor_po', 'like', "{$prefix}-{$dateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPo) {
            $nextNumber = '0001';
        } else {
            $lastNumber = explode('-', $lastPo->nomor_po);
            $nextNumber = str_pad((int) end($lastNumber) + 1, 4, '0', STR_PAD_LEFT);
        }

        return "{$prefix}-{$dateStr}-{$nextNumber}";
    }

    /**
     * Create a PO with details.
     * Wrapped in DB transaction for data integrity.
     */
    public function create(array $data, array $details): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $details) {
            $po = PurchaseOrder::create($data);

            foreach ($details as $detail) {
                $detail['barang_masuk'] = 0;
                $detail['sisa'] = $detail['jumlah'];
                $po->detail()->create($detail);
            }

            return $po->load('detail.produk');
        });
    }

    /**
     * Update a PO.
     * Prevents editing if any items have been received.
     * Wrapped in DB transaction for data integrity.
     */
    public function update(PurchaseOrder $po, array $data, array $details): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $data, $details) {
            // Cegah edit jika sudah ada barang yang diterima
            if ($po->detail()->where('barang_masuk', '>', 0)->exists()) {
                throw new \Exception('PO tidak bisa diedit karena sudah ada barang yang diterima.');
            }

            $po->update($data);

            // Reset detail (safe karena belum ada yang diterima)
            $po->detail()->delete();
            foreach ($details as $detail) {
                $detail['barang_masuk'] = 0;
                $detail['sisa'] = $detail['jumlah'];
                $po->detail()->create($detail);
            }

            return $po->fresh()->load('detail.produk');
        });
    }

    /**
     * Delete a PO.
     * Prevents deletion if any items have been received.
     */
    public function delete(PurchaseOrder $po): bool
    {
        if ($po->detail()->where('barang_masuk', '>', 0)->exists()) {
            throw new \Exception('PO tidak bisa dihapus karena sudah ada barang yang diterima.');
        }

        return DB::transaction(function () use ($po) {
            $po->detail()->delete();
            return $po->delete();
        });
    }

    /**
     * Get count of pending POs.
     */
    public function getPendingCount(): int
    {
        return PurchaseOrder::whereIn('status', ['draft', 'dikirim', 'sebagian'])->count();
    }

    /**
     * Get pending POs list.
     */
    public function getPendingPOs()
    {
        return PurchaseOrder::with(['supplier', 'detail.produk'])
            ->whereIn('status', ['draft', 'dikirim', 'sebagian'])
            ->latest()
            ->get();
    }
}
