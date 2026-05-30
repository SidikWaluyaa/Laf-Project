<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\Penjualan;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get asset value distribution per category.
     */
    public function getAssetCategoryDistribution(): array
    {
        $assetByCategory = DB::table('produk')
            ->join('kategori', 'produk.kategori_id', '=', 'kategori.id')
            ->join('stok_produk', 'produk.id', '=', 'stok_produk.produk_id')
            ->whereNull('produk.deleted_at')
            ->select('kategori.nama_kategori', DB::raw('SUM(produk.hpp * stok_produk.total_stok) as total_nilai'))
            ->groupBy('kategori.id', 'kategori.nama_kategori')
            ->having('total_nilai', '>', 0)
            ->get();
            
        return [
            'labels' => $assetByCategory->pluck('nama_kategori')->toArray(),
            'data' => $assetByCategory->pluck('total_nilai')->toArray(),
        ];
    }

    /**
     * Get top 5 selling products in the last 30 days.
     */
    public function getTopSellingProducts()
    {
        return DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->join('produk', 'penjualan_detail.produk_id', '=', 'produk.id')
            ->select('produk.kode_barang', 'produk.nama_barang', DB::raw('SUM(penjualan_detail.qty_keluar) as total_keluar'))
            ->where('penjualan.tanggal', '>=', now()->subDays(30))
            ->groupBy('produk.id', 'produk.kode_barang', 'produk.nama_barang')
            ->orderByDesc('total_keluar')
            ->limit(5)
            ->get();
    }

    /**
     * Get dead stock products (stock > 0 but no sales in 60 days).
     * Optimized with SQL subquery instead of loading all products to memory.
     */
    public function getDeadStockProducts()
    {
        $activeProductIds = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->where('penjualan.tanggal', '>=', now()->subDays(60))
            ->distinct()
            ->pluck('produk_id');

        return DB::table('produk')
            ->join('stok_produk', 'produk.id', '=', 'stok_produk.produk_id')
            ->whereNull('produk.deleted_at')
            ->whereNotIn('produk.id', $activeProductIds)
            ->select('produk.kode_barang', 'produk.nama_barang', DB::raw('SUM(stok_produk.total_stok) as total_stok'))
            ->groupBy('produk.id', 'produk.kode_barang', 'produk.nama_barang')
            ->having('total_stok', '>', 0)
            ->orderByDesc('total_stok')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent warehouse activity (combined goods in & sales).
     */
    public function getRecentActivity()
    {
        $masuk = BarangMasuk::with('admin')->latest()->take(5)->get()->map(function($item) {
            return (object)[
                'waktu' => $item->created_at,
                'tipe' => 'Masuk',
                'deskripsi' => "Terima Barang (" . ($item->nomor_surat_jalan ?? 'Doc') . ") oleh " . ($item->admin->name ?? 'Admin'),
                'icon' => '📥',
                'color' => '#10b981'
            ];
        });
        
        $keluar = Penjualan::with('admin')->latest()->take(5)->get()->map(function($item) {
            return (object)[
                'waktu' => $item->created_at,
                'tipe' => 'Keluar',
                'deskripsi' => "Penjualan ({$item->nomor_nota}) oleh " . ($item->admin->name ?? 'Admin'),
                'icon' => '🚀',
                'color' => '#3b82f6'
            ];
        });
        
        return $masuk->concat($keluar)->sortByDesc('waktu')->take(6);
    }
}
