<?php

namespace App\Http\Controllers;

use App\Models\BarangMasukDetail;
use App\Models\PenjualanDetail;
use App\Models\Supplier;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatTanggalController extends Controller
{
    public function index(Request $request)
    {
        // Default: today
        $dari   = $request->dari   ?? now()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');
        $tab    = $request->tab    ?? 'masuk';

        // ─── MASUK ──────────────────────────────────
        $queryMasuk = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk'])
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->select('barang_masuk_detail.*')
            ->whereBetween('barang_masuk.tanggal', [$dari, $sampai])
            ->orderBy('barang_masuk.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $queryMasuk->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }

        $totalQtyMasuk  = (clone $queryMasuk)->sum('barang_masuk_detail.qty_masuk');
        $totalNilaiMasuk = (clone $queryMasuk)->sum(DB::raw('barang_masuk_detail.qty_masuk * barang_masuk_detail.harga_beli'));
        $riwayatMasuk   = $queryMasuk->paginate(20, ['*'], 'masuk_page')->appends($request->all());

        // ─── KELUAR ─────────────────────────────────
        $queryKeluar = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk'])
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->select('penjualan_detail.*')
            ->whereBetween('penjualan.tanggal', [$dari, $sampai])
            ->orderBy('penjualan.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $queryKeluar->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }

        $totalQtyKeluar  = (clone $queryKeluar)->sum('penjualan_detail.qty_keluar');
        $totalNilaiKeluar = (clone $queryKeluar)->sum(DB::raw('penjualan_detail.qty_keluar * penjualan_detail.hpp_snapshot'));
        $riwayatKeluar   = $queryKeluar->paginate(20, ['*'], 'keluar_page')->appends($request->all());

        return view('riwayat-tanggal.index', compact(
            'dari', 'sampai', 'tab',
            'riwayatMasuk', 'totalQtyMasuk', 'totalNilaiMasuk',
            'riwayatKeluar', 'totalQtyKeluar', 'totalNilaiKeluar'
        ));
    }

    public function exportPdf(Request $request)
    {
        $dari   = $request->dari   ?? now()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');
        $tab    = $request->tab    ?? 'masuk';

        if ($tab === 'masuk') {
            $query = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk'])
                ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
                ->select('barang_masuk_detail.*')
                ->whereBetween('barang_masuk.tanggal', [$dari, $sampai])
                ->orderBy('barang_masuk.tanggal', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                    ->orWhere('nama_barang', 'like', "%{$s}%")
                    ->orWhere('variasi_barang', 'like', "%{$s}%"));
            }

            $data = $query->get();
            $totalQty = $data->sum('qty_masuk');
            $totalNilai = $data->sum(fn($r) => $r->qty_masuk * $r->harga_beli);
        } else {
            $query = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk'])
                ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
                ->select('penjualan_detail.*')
                ->whereBetween('penjualan.tanggal', [$dari, $sampai])
                ->orderBy('penjualan.tanggal', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                    ->orWhere('nama_barang', 'like', "%{$s}%")
                    ->orWhere('variasi_barang', 'like', "%{$s}%"));
            }

            $data = $query->get();
            $totalQty = $data->sum('qty_keluar');
            $totalNilai = $data->sum(fn($r) => $r->qty_keluar * $r->hpp_snapshot);
        }

        $tabLabel = $tab === 'masuk' ? 'Masuk' : 'Keluar';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.riwayat-tanggal', [
            'title' => "Laporan Riwayat {$tabLabel} Per Tanggal",
            'filterInfo' => "Periode: <span>{$dari}</span> s/d <span>{$sampai}</span>",
            'data' => $data,
            'totalQty' => $totalQty,
            'totalNilai' => $totalNilai,
            'tab' => $tab,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_Tanggal_{$tabLabel}_" . now()->format('Ymd_His') . '.pdf');
    }
}
