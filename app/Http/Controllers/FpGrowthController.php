<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanShopee;
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
            'results' => session('results', []),
            'topRecommendations' => session('topRecommendations', []),
            'selectedSumberData' => old('sumber_data', session('sumber_data', 'shopee')),
            'groupBy' => old('group_by', session('group_by', 'parent_sku')),
            'startDate' => old('start_date', session('start_date', date('Y-05-01'))),
            'endDate' => old('end_date', session('end_date', date('Y-05-31'))),
            'minSupport' => old('min_support', session('min_support', '0.05')),
            'minConfidence' => old('min_confidence', session('min_confidence', '10')),
            'abaikanPacking' => old('abaikan_packing', session('abaikan_packing', true)),
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'min_support' => 'required|numeric|min:0.01|max:100',
            'min_confidence' => 'required|numeric|min:0.1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'sumber_data' => 'required|in:shopee,kasir,semua',
            'group_by' => 'required|in:parent_sku,full_name',
        ]);

        $abaikanPacking = $request->boolean('abaikan_packing', true);
        $groupBy = $request->input('group_by', 'parent_sku');
        $packingKeywords = ['bubble', 'kardus', 'packing', 'pengaman', 'pelindung paket', 'dus', 'biaya packing'];

        $transactions = [];
        $sumberLabel = '';

        // 1. Data dari Shopee Marketplace
        if (in_array($request->sumber_data, ['shopee', 'semua'])) {
            $penjualanShopee = PenjualanShopee::with('detail')
                ->where('status_pesanan', 'Selesai')
                ->whereBetween('waktu_pesanan_dibuat', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ])
                ->get();

            foreach ($penjualanShopee as $p) {
                $basket = [];
                foreach ($p->detail as $d) {
                    $namaProdukBase = trim((string)$d->nama_produk);
                    if (empty($namaProdukBase)) {
                        continue;
                    }

                    if ($abaikanPacking) {
                        $namaLower = strtolower($namaProdukBase);
                        $isPacking = false;
                        foreach ($packingKeywords as $kw) {
                            if (str_contains($namaLower, $kw)) {
                                $isPacking = true;
                                break;
                            }
                        }
                        if ($isPacking) {
                            continue;
                        }
                    }

                    // Tentukan identifier item berdasarkan mode Group By
                    if ($groupBy === 'parent_sku') {
                        $itemIdentifier = !empty($d->sku_induk) ? trim($d->sku_induk) : $namaProdukBase;
                    } elseif ($groupBy === 'full_name' && !empty($d->nama_variasi)) {
                        $itemIdentifier = $namaProdukBase . ' (' . trim($d->nama_variasi) . ')';
                    } else {
                        $itemIdentifier = $namaProdukBase;
                    }

                    $basket[] = $itemIdentifier;
                }
                if (!empty($basket)) {
                    $transactions[] = [
                        'items' => $basket,
                        'meta' => [
                            'no_pesanan' => $p->no_pesanan,
                            'waktu' => \Carbon\Carbon::parse($p->waktu_pesanan_dibuat)->format('d/m/Y H:i'),
                            'username' => $p->username_pembeli ?: '-',
                            'kota' => $p->kota ?: '-',
                            'sumber' => 'Shopee',
                        ]
                    ];
                }
            }
        }

        // 2. Data dari Kasir Offline
        if (in_array($request->sumber_data, ['kasir', 'semua'])) {
            $penjualanOffline = Penjualan::with('detail.produk', 'pelanggan')
                ->whereBetween('tanggal', [$request->start_date, $request->end_date])
                ->get();

            foreach ($penjualanOffline as $p) {
                $basket = [];
                foreach ($p->detail as $d) {
                    if ($d->produk) {
                        $namaProdukBase = trim((string)$d->produk->nama_barang);
                        if (empty($namaProdukBase)) {
                            continue;
                        }

                        if ($abaikanPacking) {
                            $namaLower = strtolower($namaProdukBase);
                            $isPacking = false;
                            foreach ($packingKeywords as $kw) {
                                if (str_contains($namaLower, $kw)) {
                                    $isPacking = true;
                                    break;
                                }
                            }
                            if ($isPacking) {
                                continue;
                            }
                        }

                        $basket[] = $namaProdukBase;
                    }
                }
                if (!empty($basket)) {
                    $transactions[] = [
                        'items' => $basket,
                        'meta' => [
                            'no_pesanan' => $p->nomor_nota ?: ('NOT-' . $p->id),
                            'waktu' => \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y'),
                            'username' => $p->pelanggan ? $p->pelanggan->nama : 'Pelanggan Kasir',
                            'kota' => 'Kasir Offline',
                            'sumber' => 'POS',
                        ]
                    ];
                }
            }
        }

        if ($request->sumber_data === 'shopee') {
            $sumberLabel = 'Shopee Marketplace';
        } elseif ($request->sumber_data === 'kasir') {
            $sumberLabel = 'Penjualan Kasir (POS)';
        } else {
            $sumberLabel = 'Gabungan (Shopee & Kasir)';
        }

        if (empty($transactions)) {
            return back()->withInput()->with('error', "Tidak ada data transaksi [{$sumberLabel}] pada rentang tanggal tersebut.");
        }

        // 3. Jalankan Algoritma FP-Growth murni melalui Service
        $fpService = new \App\Services\FpGrowthService();
        $results = $fpService->run(
            $transactions, 
            $request->min_support, 
            $request->min_confidence
        );

        if (empty($results)) {
            return back()->withInput()->with([
                'error' => "Analisis selesai, namun tidak ditemukan pola yang memenuhi batas Minimum Support & Confidence dari {$sumberLabel}. Coba turunkan nilai parameternya.",
                'sumber_data' => $request->sumber_data,
                'group_by' => $groupBy,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'min_support' => $request->min_support,
                'min_confidence' => $request->min_confidence,
                'abaikan_packing' => $abaikanPacking,
            ]);
        }

        // 4. Buat Ringkasan Rekomendasi Promo Teratas (Executive Summary)
        $topRecommendations = [];
        $seenPairs = [];

        foreach ($results as $r) {
            $pairKey = [$r['ante'], $r['cons']];
            sort($pairKey);
            $pairKeyStr = implode('|||', $pairKey);

            if (isset($seenPairs[$pairKeyStr])) {
                continue;
            }
            $seenPairs[$pairKeyStr] = true;

            $anteLower = strtolower($r['ante']);
            $consLower = strtolower($r['cons']);

            $kategori = 'Cross-Selling Produk';
            $saranAksi = "Buat paket bundling kombo hemat atau tampilkan rekomendasi produk 'Sering Dibeli Bersama' di deskripsi toko.";

            if (str_contains($anteLower, 'kaos kaki') && str_contains($consLower, 'kaos kaki')) {
                $kategori = 'Paket Combo Warna / Variasi';
                $saranAksi = "Buat produk 'Paket Hemat Isi Multi-Warna/Variasi' di Shopee Seller Centre (cth: Paket Hitam & Putih).";
            } elseif ((str_contains($anteLower, 'parfum') || str_contains($consLower, 'parfum')) || (str_contains($anteLower, 'sarung') || str_contains($consLower, 'sarung'))) {
                $kategori = 'Paket Add-On Aksesoris / Perawatan';
                $saranAksi = "Aktifkan fitur Shopee 'Kombo Hemat (Add-On Deal)' untuk diskon produk perawatan/aksesoris saat membeli produk utama.";
            } elseif ((str_contains($anteLower, 'sepatu') && str_contains($consLower, 'kaos kaki')) || (str_contains($anteLower, 'kaos kaki') && str_contains($consLower, 'sepatu'))) {
                $kategori = 'Paket Back to School / Outfit Match';
                $saranAksi = "Buat paket bundling Sepatu + Kaos Kaki dengan harga khusus atau bonus langsung saat checkout.";
            }

            $topRecommendations[] = [
                'ante' => $r['ante'],
                'cons' => $r['cons'],
                'support' => $r['support'],
                'confidence' => $r['confidence'],
                'lift_ratio' => $r['lift_ratio'],
                'count_both' => $r['count_both'],
                'kategori' => $kategori,
                'saran_aksi' => $saranAksi,
            ];

            if (count($topRecommendations) >= 4) {
                break;
            }
        }

        return back()->withInput()->with([
            'success' => 'Analisis FP-Growth berhasil diselesaikan menggunakan ' . count($transactions) . " data keranjang transaksi riil ({$sumberLabel})." . ($abaikanPacking ? ' (Item packing diabaikan)' : ''),
            'results' => $results,
            'topRecommendations' => $topRecommendations,
            'sumber_data' => $request->sumber_data,
            'group_by' => $groupBy,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_support' => $request->min_support,
            'min_confidence' => $request->min_confidence,
            'abaikan_packing' => $abaikanPacking,
        ]);
    }
}
