<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\StokProduk;
use Illuminate\Support\Facades\DB;

class ValuationService
{
    /**
     * Get total asset value across all products (hpp × total_stok).
     * Uses SQL aggregate instead of loading all models to memory.
     */
    public function getTotalAssetValue(): float
    {
        return (float) DB::table('produk')
            ->join('stok_produk', 'produk.id', '=', 'stok_produk.produk_id')
            ->whereNull('produk.deleted_at')
            ->sum(DB::raw('produk.hpp * stok_produk.total_stok'));
    }

    /**
     * Get asset valuation list per product.
     * Uses SQL aggregate for total_stok and nilai calculation.
     */
    public function getValuationList(?int $kategoriId = null)
    {
        return Produk::with(['stokProduk.lokasi', 'kategori', 'satuanRelasi'])
            ->select('produk.*')
            ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
            ->selectSub(
                StokProduk::selectRaw('COALESCE(SUM(total_stok), 0)')
                    ->whereColumn('stok_produk.produk_id', 'produk.id'),
                'total_stok_all'
            )
            ->get()
            ->map(function ($produk) {
                return (object) [
                    'id' => $produk->id,
                    'kode_barang' => $produk->kode_barang,
                    'nama_barang' => $produk->nama_barang,
                    'variasi_barang' => $produk->variasi_barang,
                    'kategori' => $produk->kategori->nama_kategori ?? '-',
                    'satuan' => $produk->satuan_label,
                    'hpp' => $produk->hpp,
                    'total_stok' => (int) $produk->total_stok_all,
                    'nilai' => $produk->hpp * $produk->total_stok_all,
                    'stok_per_lokasi' => $produk->stokProduk->map(function ($sp) {
                        return (object) [
                            'lokasi' => $sp->lokasi->nama_lokasi ?? '-',
                            'stok' => $sp->total_stok,
                        ];
                    }),
                ];
            });
    }
}
