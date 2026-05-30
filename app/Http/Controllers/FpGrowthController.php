<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class FpGrowthController extends Controller
{
    /**
     * Tampilkan halaman utama analisis FP-Growth.
     */
    public function index()
    {
        return view('fp-growth.index', [
            'title' => 'Analisis Promo (FP-Growth)',
            'results' => session('results', [])
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'min_support' => 'required|numeric|min:0.1|max:100',
            'min_confidence' => 'required|numeric|min:0.1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // 1. Ambil data transaksi riil dari database
        $penjualan = Penjualan::with('detail.produk')
            ->whereBetween('tanggal', [$request->start_date, $request->end_date])
            ->get();

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Tidak ada data transaksi pada rentang tanggal tersebut.');
        }

        // 2. Format data menjadi array transaksi (Keranjang Belanja)
        $transactions = [];
        foreach ($penjualan as $p) {
            $basket = [];
            foreach ($p->detail as $d) {
                if ($d->produk) {
                    $basket[] = $d->produk->nama_barang;
                }
            }
            if (!empty($basket)) {
                $transactions[] = $basket;
            }
        }

        // 3. Jalankan Algoritma FP-Growth murni melalui Service
        $fpService = new \App\Services\FpGrowthService();
        $results = $fpService->run(
            $transactions, 
            $request->min_support, 
            $request->min_confidence
        );

        if (empty($results)) {
            return back()->with('error', 'Analisis selesai, namun tidak ditemukan pola yang memenuhi batas Minimum Support & Confidence. Coba turunkan nilai parameternya.');
        }

        return back()->with([
            'success' => 'Analisis FP-Growth berhasil diselesaikan menggunakan ' . count($transactions) . ' data transaksi riil.',
            'results' => $results
        ]);
    }
}
