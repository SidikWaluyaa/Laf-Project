<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\BarangMasukDetail;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;

class RiwayatSupplierPelangganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->tab ?? 'supplier';
        $suppliers   = Supplier::orderBy('nama_supplier')->get();
        $pelangganList = Pelanggan::orderBy('nama_pelanggan')->get();

        // ─── SUPPLIER TAB ────────────────────────────
        $supplierId = $request->supplier_id ?: ($suppliers->first()->id ?? null);
        $supplierAktif = Supplier::find($supplierId);

        $querySupplier = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk'])
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->select('barang_masuk_detail.*')
            ->where('barang_masuk.supplier_id', $supplierId)
            ->orderBy('barang_masuk.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $querySupplier->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }
        if ($request->filled('dari')) {
            $querySupplier->where('barang_masuk.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $querySupplier->where('barang_masuk.tanggal', '<=', $request->sampai);
        }

        $totalQtySupplier = (clone $querySupplier)->sum('barang_masuk_detail.qty_masuk');
        $riwayatSupplier  = $querySupplier->paginate(20, ['*'], 'supplier_page')->appends($request->all());

        // ─── PELANGGAN TAB ───────────────────────────
        $pelangganId = $request->pelanggan_id ?: ($pelangganList->first()->id ?? null);
        $pelangganAktif = Pelanggan::find($pelangganId);

        $queryPelanggan = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk'])
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->select('penjualan_detail.*')
            ->where('penjualan.pelanggan_id', $pelangganId)
            ->orderBy('penjualan.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $queryPelanggan->whereHas('produk', fn($q) => $q->where('kode_barang', 'like', "%{$s}%")
                ->orWhere('nama_barang', 'like', "%{$s}%")
                ->orWhere('variasi_barang', 'like', "%{$s}%"));
        }
        if ($request->filled('dari')) {
            $queryPelanggan->where('penjualan.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $queryPelanggan->where('penjualan.tanggal', '<=', $request->sampai);
        }

        $totalQtyPelanggan = (clone $queryPelanggan)->sum('penjualan_detail.qty_keluar');
        $riwayatPelanggan  = $queryPelanggan->paginate(20, ['*'], 'pelanggan_page')->appends($request->all());

        return view('riwayat-supplier-pelanggan.index', compact(
            'tab', 'suppliers', 'pelangganList',
            'supplierId', 'supplierAktif', 'riwayatSupplier', 'totalQtySupplier',
            'pelangganId', 'pelangganAktif', 'riwayatPelanggan', 'totalQtyPelanggan'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tab = $request->tab ?? 'supplier';

        if ($tab === 'supplier') {
            $supplierId = $request->supplier_id;
            $supplier = Supplier::find($supplierId);
            $entityName = $supplier->nama_supplier ?? '-';

            $query = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk'])
                ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
                ->select('barang_masuk_detail.*')
                ->where('barang_masuk.supplier_id', $supplierId)
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
            $title = "Laporan Riwayat Supplier — {$entityName}";
        } else {
            $pelangganId = $request->pelanggan_id;
            $pelanggan = Pelanggan::find($pelangganId);
            $entityName = $pelanggan->nama_pelanggan ?? '-';

            $query = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk'])
                ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
                ->select('penjualan_detail.*')
                ->where('penjualan.pelanggan_id', $pelangganId)
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
            $title = "Laporan Riwayat Pelanggan — {$entityName}";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.riwayat-supplier-pelanggan', [
            'title' => $title,
            'filterInfo' => ($tab === 'supplier' ? 'Supplier' : 'Pelanggan') . ': <span>' . $entityName . '</span>',
            'data' => $data,
            'totalQty' => $totalQty,
            'entityName' => $entityName,
            'tab' => $tab,
        ])->setPaper('a4', 'landscape');

        $label = $tab === 'supplier' ? 'Supplier' : 'Pelanggan';
        return $pdf->stream("Laporan_{$label}_" . now()->format('Ymd_His') . '.pdf');
    }
}
