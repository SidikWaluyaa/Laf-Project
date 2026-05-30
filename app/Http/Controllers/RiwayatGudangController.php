<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\BarangMasukDetail;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatGudangController extends Controller
{
    public function index(Request $request)
    {
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();
        $lokasiId = $request->lokasi_id ?: ($lokasiList->first()->id ?? null);
        $lokasiAktif = Lokasi::find($lokasiId);

        // ─── Riwayat MASUK Gudang ─────────────────────
        $queryMasuk = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'produk'])
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->select('barang_masuk_detail.*')
            ->where('barang_masuk.lokasi_id', $lokasiId)
            ->orderBy('barang_masuk.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $queryMasuk->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }

        if ($request->filled('dari')) {
            $queryMasuk->where('barang_masuk.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryMasuk->where('barang_masuk.tanggal', '<=', $request->sampai);
        }

        $totalMasuk = (clone $queryMasuk)->sum('barang_masuk_detail.qty_masuk');
        $riwayatMasuk = $queryMasuk->paginate(15, ['*'], 'masuk_page')->appends($request->all());

        // ─── Riwayat KELUAR Gudang ────────────────────
        $queryKeluar = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'produk'])
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->select('penjualan_detail.*')
            ->where('penjualan.lokasi_id', $lokasiId)
            ->orderBy('penjualan.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $queryKeluar->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }

        if ($request->filled('dari')) {
            $queryKeluar->where('penjualan.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryKeluar->where('penjualan.tanggal', '<=', $request->sampai);
        }

        $totalKeluar = (clone $queryKeluar)->sum('penjualan_detail.qty_keluar');
        $riwayatKeluar = $queryKeluar->paginate(15, ['*'], 'keluar_page')->appends($request->all());

        // Tab aktif
        $tab = $request->tab ?? 'masuk';

        return view('riwayat-gudang.index', compact(
            'lokasiList', 'lokasiAktif', 'lokasiId',
            'riwayatMasuk', 'totalMasuk',
            'riwayatKeluar', 'totalKeluar',
            'tab'
        ));
    }

    public function exportPdf(Request $request)
    {
        $lokasiId = $request->lokasi_id;
        $lokasi = Lokasi::find($lokasiId);
        $tab = $request->tab ?? 'masuk';

        if ($tab === 'masuk') {
            $query = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk'])
                ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
                ->select('barang_masuk_detail.*')
                ->where('barang_masuk.lokasi_id', $lokasiId)
                ->orderBy('barang_masuk.tanggal', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                    ->orWhere('nama_barang', 'like', "%{$s}%")
                    ->orWhere('variasi_barang', 'like', "%{$s}%"));
            }
            if ($request->filled('dari')) $query->where('barang_masuk.tanggal', '>=', $request->dari);
            if ($request->filled('sampai')) $query->where('barang_masuk.tanggal', '<=', $request->sampai);

            $data = $query->get();
            $totalQty = $data->sum('qty_masuk');
        } else {
            $query = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk'])
                ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
                ->select('penjualan_detail.*')
                ->where('penjualan.lokasi_id', $lokasiId)
                ->orderBy('penjualan.tanggal', 'desc');

            if ($request->filled('search')) {
                $s = $request->search;
                $query->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                    ->orWhere('nama_barang', 'like', "%{$s}%")
                    ->orWhere('variasi_barang', 'like', "%{$s}%"));
            }
            if ($request->filled('dari')) $query->where('penjualan.tanggal', '>=', $request->dari);
            if ($request->filled('sampai')) $query->where('penjualan.tanggal', '<=', $request->sampai);

            $data = $query->get();
            $totalQty = $data->sum('qty_keluar');
        }

        $tabLabel = $tab === 'masuk' ? 'Masuk' : 'Keluar';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.riwayat-gudang', [
            'title' => "Laporan Riwayat {$tabLabel} Gudang — " . ($lokasi->nama_lokasi ?? ''),
            'filterInfo' => 'Lokasi: <span>' . ($lokasi->nama_lokasi ?? '-') . '</span>',
            'data' => $data,
            'totalQty' => $totalQty,
            'tab' => $tab,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_Gudang_{$tabLabel}_" . now()->format('Ymd_His') . '.pdf');
    }
}
