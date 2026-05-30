<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\BarangMasukDetail;
use App\Models\PenjualanDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * List all produk with search and pagination.
     */
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Produk::with(['kategori', 'stokProduk.lokasi', 'stokMinimum'])
            ->when($search, function ($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                      ->orWhere('kode_barang', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a produk by ID.
     */
    public function find(int $id): Produk
    {
        return Produk::with(['kategori', 'stokProduk.lokasi', 'stokMinimum'])->findOrFail($id);
    }

    /**
     * Get stock mutation history for a product (masuk + keluar combined).
     */
    public function getMutasiStok(int $produkId)
    {
        // Barang masuk
        $masuk = BarangMasukDetail::where('produk_id', $produkId)
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->whereNull('barang_masuk.deleted_at')
            ->select(
                DB::raw("'MASUK' as tipe"),
                'barang_masuk.tanggal',
                'barang_masuk_detail.qty_masuk as qty',
                'barang_masuk_detail.harga_beli as harga',
                DB::raw('NULL as hpp_snapshot'),
                'barang_masuk.keterangan'
            )->get();

        // Penjualan
        $keluar = PenjualanDetail::where('produk_id', $produkId)
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->whereNull('penjualan.deleted_at')
            ->select(
                DB::raw("'KELUAR' as tipe"),
                'penjualan.tanggal',
                'penjualan_detail.qty_keluar as qty',
                DB::raw('NULL as harga'),
                'penjualan_detail.hpp_snapshot',
                'penjualan.keterangan'
            )->get();

        return $masuk->concat($keluar)->sortByDesc('tanggal')->values()->take(50);
    }

    /**
     * Generate kode barang otomatis berdasarkan kategori.
     * Format: LAF-{PREFIX}-{001}
     */
    public function generateKodeBarang(int $kategoriId): string
    {
        $kategori = Kategori::findOrFail($kategoriId);
        $prefix = strtoupper($kategori->kode_prefix);

        // Cari nomor urut terakhir untuk prefix ini
        $lastProduk = Produk::where('kode_barang', 'like', "LAF-{$prefix}-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(kode_barang, '-', -1) AS UNSIGNED) DESC")
            ->first();

        if ($lastProduk) {
            // Ambil angka terakhir dari format LAF-XX-001
            $lastNumber = (int) substr($lastProduk->kode_barang, strrpos($lastProduk->kode_barang, '-') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('LAF-%s-%03d', $prefix, $nextNumber);
    }

    /**
     * Create a new produk with auto-generated kode_barang.
     */
    public function create(array $data): Produk
    {
        $data['kode_barang'] = $this->generateKodeBarang($data['kategori_id']);
        return Produk::create($data);
    }

    /**
     * Update a produk. Re-generate kode if kategori changed.
     */
    public function update(Produk $produk, array $data): Produk
    {
        // Jika kategori berubah, generate ulang kode barang
        if (isset($data['kategori_id']) && $data['kategori_id'] != $produk->kategori_id) {
            $data['kode_barang'] = $this->generateKodeBarang($data['kategori_id']);
        }

        $produk->update($data);
        return $produk->fresh();
    }

    /**
     * Soft delete a produk.
     */
    public function delete(Produk $produk): bool
    {
        return $produk->delete();
    }

    /**
     * Get total produk count.
     */
    public function count(): int
    {
        return Produk::count();
    }
}
