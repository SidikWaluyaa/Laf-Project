<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BarangMasukDetail;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArusBarangController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 25;

        // ─── Top Masuk (most restocked) ────────────────
        $queryMasuk = BarangMasukDetail::select(
                'produk_id',
                DB::raw('SUM(qty_masuk) as total_masuk')
            )
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id');

        if ($request->filled('dari')) {
            $queryMasuk->where('barang_masuk.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryMasuk->where('barang_masuk.tanggal', '<=', $request->sampai);
        }

        $topMasuk = $queryMasuk
            ->groupBy('produk_id')
            ->orderByDesc('total_masuk')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $produk = Produk::with('stokProduk')->find($item->produk_id);
                return (object)[
                    'produk' => $produk,
                    'total_masuk' => $item->total_masuk,
                    'stok_terkini' => $produk ? $produk->total_stok : 0,
                ];
            });

        // ─── Top Keluar (best sellers) ─────────────────
        $queryKeluar = PenjualanDetail::select(
                'produk_id',
                DB::raw('SUM(qty_keluar) as total_keluar')
            )
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id');

        if ($request->filled('dari')) {
            $queryKeluar->where('penjualan.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryKeluar->where('penjualan.tanggal', '<=', $request->sampai);
        }

        $topKeluar = $queryKeluar
            ->groupBy('produk_id')
            ->orderByDesc('total_keluar')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $produk = Produk::with('stokProduk')->find($item->produk_id);
                return (object)[
                    'produk' => $produk,
                    'total_keluar' => $item->total_keluar,
                    'stok_terkini' => $produk ? $produk->total_stok : 0,
                ];
            });

        return view('arus-barang.index', compact('topMasuk', 'topKeluar', 'limit'));
    }

    public function exportPdf(Request $request)
    {
        $limit = $request->limit ?? 25;

        $queryMasuk = BarangMasukDetail::select('produk_id', DB::raw('SUM(qty_masuk) as total_masuk'))
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id');
        if ($request->filled('dari')) $queryMasuk->where('barang_masuk.tanggal', '>=', $request->dari);
        if ($request->filled('sampai')) $queryMasuk->where('barang_masuk.tanggal', '<=', $request->sampai);
        $topMasuk = $queryMasuk->groupBy('produk_id')->orderByDesc('total_masuk')->limit($limit)->get()
            ->map(fn($item) => (object)[
                'produk' => Produk::with('stokProduk')->find($item->produk_id),
                'total_masuk' => $item->total_masuk,
                'stok_terkini' => Produk::with('stokProduk')->find($item->produk_id)?->total_stok ?? 0,
            ]);

        $queryKeluar = PenjualanDetail::select('produk_id', DB::raw('SUM(qty_keluar) as total_keluar'))
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id');
        if ($request->filled('dari')) $queryKeluar->where('penjualan.tanggal', '>=', $request->dari);
        if ($request->filled('sampai')) $queryKeluar->where('penjualan.tanggal', '<=', $request->sampai);
        $topKeluar = $queryKeluar->groupBy('produk_id')->orderByDesc('total_keluar')->limit($limit)->get()
            ->map(fn($item) => (object)[
                'produk' => Produk::with('stokProduk')->find($item->produk_id),
                'total_keluar' => $item->total_keluar,
                'stok_terkini' => Produk::with('stokProduk')->find($item->produk_id)?->total_stok ?? 0,
            ]);

        $filters = [];
        if ($request->filled('dari')) $filters[] = 'Dari: <span>' . $request->dari . '</span>';
        if ($request->filled('sampai')) $filters[] = 'Sampai: <span>' . $request->sampai . '</span>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.arus-barang', [
            'title' => 'Laporan Arus Barang',
            'filterInfo' => count($filters) ? implode(' &bull; ', $filters) : 'Semua periode',
            'topMasuk' => $topMasuk,
            'topKeluar' => $topKeluar,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Arus_Barang_' . now()->format('Ymd_His') . '.pdf');
    }
}
