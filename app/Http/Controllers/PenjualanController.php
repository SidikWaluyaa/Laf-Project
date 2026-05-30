<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use App\Http\Requests\StorePenjualanRequest;
use App\Models\Pelanggan;
use App\Models\Lokasi;
use App\Models\Produk;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function __construct(
        private SalesService $salesService
    ) {}

    public function index(Request $request)
    {
        $penjualan = $this->salesService->list($request->search);
        return view('penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        $pelanggan = Pelanggan::select('id', 'nama_pelanggan')->orderBy('nama_pelanggan')->get();
        $lokasi = Lokasi::select('id', 'nama_lokasi')->orderBy('nama_lokasi')->get();
        $produk = Produk::select('id', 'kode_barang', 'nama_barang', 'variasi_barang', 'hpp')->orderBy('nama_barang')->get();
        
        // Pass map of stock availability for JS Validation: [lokasi_id][produk_id] = total_stok
        $stokMap = \App\Models\StokProduk::all()->groupBy('lokasi_id')->map(function ($items) {
            return $items->keyBy('produk_id')->map->total_stok;
        })->toArray();
        
        $autoNomorNota = $this->salesService->generateNomorNota();
        return view('penjualan.create', compact('pelanggan', 'lokasi', 'produk', 'autoNomorNota', 'stokMap'));
    }

    public function store(StorePenjualanRequest $request)
    {
        try {
            $data = $request->only(['tanggal', 'tipe_nota', 'pelanggan_id', 'nomor_nota', 'lokasi_id', 'keterangan']);
            $data['admin_id'] = Auth::id();

            $this->salesService->create($data, $request->details);

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $penjualan = $this->salesService->find($id);
        return view('penjualan.show', compact('penjualan'));
    }

    public function void(int $id)
    {
        try {
            $penjualan = $this->salesService->find($id);
            $this->salesService->void($penjualan);
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil di-void. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PenjualanDetail::with(['penjualan.pelanggan', 'penjualan.admin', 'penjualan.lokasi', 'produk.kategori'])
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->select('penjualan_detail.*')
            ->whereNull('penjualan.deleted_at')
            ->orderBy('penjualan.tanggal', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('produk', function ($q) use ($s) {
                $q->where('kode_barang', 'like', "%{$s}%")
                  ->orWhere('nama_barang', 'like', "%{$s}%");
            });
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('penjualan.pelanggan_id', $request->pelanggan_id);
        }

        if ($request->filled('lokasi_id')) {
            $query->where('penjualan.lokasi_id', $request->lokasi_id);
        }

        if ($request->filled('dari')) $query->where('penjualan.tanggal', '>=', $request->dari);
        if ($request->filled('sampai')) $query->where('penjualan.tanggal', '<=', $request->sampai);

        return $query;
    }

    public function riwayat(Request $request)
    {
        $query = $this->buildRiwayatQuery($request);
        $riwayat = $query->paginate(25)->appends($request->all());
        $pelangganList = Pelanggan::orderBy('nama_pelanggan')->get();
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

        $statsQuery = $this->buildRiwayatQuery($request);
        $totalQty = $statsQuery->sum('penjualan_detail.qty_keluar');
        $totalNilai = $statsQuery->sum(DB::raw('penjualan_detail.qty_keluar * penjualan_detail.hpp_snapshot'));

        return view('penjualan.riwayat', compact('riwayat', 'pelangganList', 'lokasiList', 'totalQty', 'totalNilai'));
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildRiwayatQuery($request);
        $data = $query->get();

        $totalQty = $data->sum('qty_keluar');
        $totalNilai = $data->sum(fn($r) => $r->qty_keluar * $r->hpp_snapshot);

        $filters = [];
        if ($request->filled('search')) $filters[] = 'Produk: <span>' . $request->search . '</span>';
        if ($request->filled('dari')) $filters[] = 'Dari: <span>' . $request->dari . '</span>';
        if ($request->filled('sampai')) $filters[] = 'Sampai: <span>' . $request->sampai . '</span>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.riwayat-penjualan', [
            'title' => 'Laporan Riwayat Penjualan',
            'filterInfo' => count($filters) ? implode(' &bull; ', $filters) : 'Semua data',
            'data' => $data,
            'totalQty' => $totalQty,
            'totalNilai' => $totalNilai,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Riwayat_Penjualan_' . now()->format('Ymd_His') . '.pdf');
    }
}
