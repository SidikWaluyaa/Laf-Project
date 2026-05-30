<?php

namespace App\Http\Controllers;

use App\Services\ValuationService;
use App\Models\Kategori;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(
        private ValuationService $valuationService
    ) {}

    public function nilaiAset(Request $request)
    {
        $kategoriId = $request->kategori_id;
        $items = $this->valuationService->getValuationList($kategoriId);
        $totalNilai = $items->sum('nilai');
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('laporan.nilai-aset', compact('items', 'totalNilai', 'kategoriList', 'kategoriId'));
    }

    public function nilaiAsetPdf(Request $request)
    {
        $kategoriId = $request->kategori_id;
        $items = $this->valuationService->getValuationList($kategoriId);
        $totalNilai = $items->sum('nilai');

        $filters = [];
        if ($kategoriId) {
            $kat = Kategori::find($kategoriId);
            $filters[] = 'Kategori: <span>' . ($kat->nama_kategori ?? '-') . '</span>';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nilai-aset', [
            'title' => 'Laporan Nilai Aset Barang',
            'filterInfo' => count($filters) ? implode(' &bull; ', $filters) : 'Semua data',
            'items' => $items,
            'totalNilai' => $totalNilai,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Nilai_Aset_' . now()->format('Ymd_His') . '.pdf');
    }
}
