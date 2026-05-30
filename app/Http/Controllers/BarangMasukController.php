<?php

namespace App\Http\Controllers;

use App\Services\GoodsReceiptService;
use App\Http\Requests\StoreBarangMasukRequest;
use App\Models\Supplier;
use App\Models\Lokasi;
use App\Models\Produk;
use App\Models\BarangMasukDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BarangMasukController extends Controller
{
    public function __construct(
        private GoodsReceiptService $goodsReceiptService
    ) {}

    public function index(Request $request)
    {
        $barangMasuk = $this->goodsReceiptService->list($request->search);
        return view('barang-masuk.index', compact('barangMasuk'));
    }

    public function create()
    {
        $suppliers = Supplier::select('id', 'nama_supplier')->orderBy('nama_supplier')->get();
        $lokasi = Lokasi::select('id', 'nama_lokasi')->orderBy('nama_lokasi')->get();
        $produk = Produk::select('id', 'kode_barang', 'nama_barang', 'variasi_barang', 'hpp')->orderBy('nama_barang')->get();
        $autoNomorNota = $this->goodsReceiptService->generateNomorNota();
        return view('barang-masuk.create', compact('suppliers', 'lokasi', 'produk', 'autoNomorNota'));
    }

    public function store(StoreBarangMasukRequest $request)
    {
        try {
            $data = $request->only(['tanggal', 'supplier_id', 'lokasi_id', 'nomor_nota', 'keterangan']);
            $data['admin_id'] = Auth::id();

            $this->goodsReceiptService->create($data, $request->details);

            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $barangMasuk = $this->goodsReceiptService->find($id);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    public function void(int $id)
    {
        try {
            $barangMasuk = $this->goodsReceiptService->find($id);
            $this->goodsReceiptService->void($barangMasuk);
            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil di-void. Stok telah dikurangi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Build shared riwayat query with filters.
     * Extracted to avoid code duplication between riwayat() and exportPdf().
     */
    private function buildRiwayatQuery(Request $request)
    {
        $query = BarangMasukDetail::with(['barangMasuk.supplier', 'barangMasuk.admin', 'barangMasuk.lokasi', 'produk.kategori'])
            ->join('barang_masuk', 'barang_masuk_detail.barang_masuk_id', '=', 'barang_masuk.id')
            ->select('barang_masuk_detail.*')
            ->orderBy('barang_masuk.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('produk', function ($q) use ($s) {
                $q->where('kode_barang', 'like', "%{$s}%")
                  ->orWhere('nama_barang', 'like', "%{$s}%")
                  ->orWhere('variasi_barang', 'like', "%{$s}%");
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('barang_masuk.supplier_id', $request->supplier_id);
        }

        if ($request->filled('lokasi_id')) {
            $query->where('barang_masuk.lokasi_id', $request->lokasi_id);
        }

        if ($request->filled('dari')) {
            $query->where('barang_masuk.tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('barang_masuk.tanggal', '<=', $request->sampai);
        }

        return $query;
    }

    /**
     * Riwayat Barang Masuk — flat per-item history view.
     */
    public function riwayat(Request $request)
    {
        $query = $this->buildRiwayatQuery($request);

        $riwayat = $query->paginate(25)->appends($request->all());
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

        // Summary stats
        $statsQuery = $this->buildRiwayatQuery($request);
        $totalQty = $statsQuery->sum('barang_masuk_detail.qty_masuk');
        $totalNilai = $statsQuery->sum(DB::raw('barang_masuk_detail.qty_masuk * barang_masuk_detail.harga_beli'));

        return view('barang-masuk.riwayat', compact('riwayat', 'suppliers', 'lokasiList', 'totalQty', 'totalNilai'));
    }

    /**
     * Export Riwayat Barang Masuk to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = $this->buildRiwayatQuery($request);
        $data = $query->get();

        $totalQty = $data->sum('qty_masuk');
        $totalNilai = $data->sum(fn($r) => $r->qty_masuk * $r->harga_beli);

        $filters = [];
        if ($request->filled('search')) $filters[] = 'Produk: <span>' . $request->search . '</span>';
        if ($request->filled('dari')) $filters[] = 'Dari: <span>' . $request->dari . '</span>';
        if ($request->filled('sampai')) $filters[] = 'Sampai: <span>' . $request->sampai . '</span>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.riwayat-masuk', [
            'title' => 'Laporan Riwayat Barang Masuk',
            'filterInfo' => count($filters) ? implode(' &bull; ', $filters) : 'Semua data',
            'data' => $data,
            'totalQty' => $totalQty,
            'totalNilai' => $totalNilai,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Riwayat_Masuk_' . now()->format('Ymd_His') . '.pdf');
    }
}
