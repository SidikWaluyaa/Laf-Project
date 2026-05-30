<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use App\Services\ValuationService;
use App\Services\PurchaseOrderService;
use App\Services\GoodsReceiptService;
use App\Services\SalesService;
use App\Services\ProductService;
use App\Services\DashboardService;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private StockService $stockService,
        private ValuationService $valuationService,
        private PurchaseOrderService $poService,
        private GoodsReceiptService $goodsReceiptService,
        private SalesService $salesService,
        private DashboardService $dashboardService,
        private FonnteService $fonnteService,
    ) {}

    public function index()
    {
        $year = request('year', now()->year);

        $data = [
            'currentYear' => (int) $year,
            'totalProduk' => $this->productService->count(),
            'totalStok' => $this->stockService->getTotalAllStok(),
            'totalNilaiAset' => $this->valuationService->getTotalAssetValue(),
            'lowStockProducts' => $this->stockService->getLowStockProducts(),
            'pendingPOCount' => $this->poService->getPendingCount(),
            'barangMasukChart' => $this->goodsReceiptService->getMonthlyData($year),
            'barangKeluarChart' => $this->salesService->getMonthlyData($year),
        ];

        // 1. Komposisi Nilai Aset per Kategori 
        $assetCategoryData = $this->dashboardService->getAssetCategoryDistribution();
        $data['assetCategoryLabels'] = $assetCategoryData['labels'];
        $data['assetCategoryData'] = $assetCategoryData['data'];

        // 2. Top 5 Produk Paling Laris (30 hari terakhir)
        $data['topProducts'] = $this->dashboardService->getTopSellingProducts();

        // 3. Dead Stock (Stok > 0, tapi tidak ada penjualan dalam 60 hari terakhir)
        $data['deadStock'] = $this->dashboardService->getDeadStockProducts();

        // 4. Aktivitas Gudang Terkini (Gabungan PO, Masuk, Keluar)
        $data['recentActivity'] = $this->dashboardService->getRecentActivity();

        return view('dashboard', $data);
    }

    public function tutupShift()
    {
        $ownerWa = config('app.wa_target_owner', env('WA_TARGET_OWNER', ''));
        if (empty($ownerWa)) {
            return back()->with('error', 'Gagal kirim Laporan: Nomor WhatsApp Owner belum disetting di .env (WA_TARGET_OWNER).');
        }

        // 1. Ambil Ringkasan Harian
        $todayStr = now()->translatedFormat('l, d F Y');
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        // Penjualan hari ini
        $salesToday = \App\Models\Penjualan::whereBetween('tanggal', [$start, $end])
            ->where('keterangan', '!=', '[VOID]')
            ->get();
        $totalNotaSales = $salesToday->count();
        $omzetHariIni = $salesToday->sum(function ($sales) {
            return \App\Models\PenjualanDetail::where('penjualan_id', $sales->id)
                ->sum(\Illuminate\Support\Facades\DB::raw('qty_keluar * hpp_snapshot'));
        });

        // Rincian Produk Terjual (Group By Product)
        $rincianPenjualan = \Illuminate\Support\Facades\DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan.id', '=', 'penjualan_detail.penjualan_id')
            ->join('produk', 'produk.id', '=', 'penjualan_detail.produk_id')
            ->whereBetween('penjualan.tanggal', [$start, $end])
            ->where('penjualan.keterangan', '!=', '[VOID]')
            ->select(
                'produk.nama_barang', 
                \Illuminate\Support\Facades\DB::raw('SUM(penjualan_detail.qty_keluar) as total_qty'),
                \Illuminate\Support\Facades\DB::raw('SUM(penjualan_detail.qty_keluar * penjualan_detail.hpp_snapshot) as subtotal')
            )
            ->groupBy('produk.id', 'produk.nama_barang')
            ->having('total_qty', '>', 0)
            ->orderByDesc('subtotal')
            ->get();

        // Barang Masuk (Belanja) hari ini
        $goodsReceiptToday = \App\Models\BarangMasuk::whereBetween('tanggal', [$start, $end])
            ->where('keterangan', '!=', '[VOID]')
            ->get();
        $totalNotaGM = $goodsReceiptToday->count();
        $pengeluaranHariIni = $goodsReceiptToday->sum(function ($gm) {
            return \App\Models\BarangMasukDetail::where('barang_masuk_id', $gm->id)
                ->sum(\Illuminate\Support\Facades\DB::raw('qty_masuk * harga_beli'));
        });

        // Rincian Produk Masuk/Dibeli (Group By Product)
        $rincianBelanja = \Illuminate\Support\Facades\DB::table('barang_masuk_detail')
            ->join('barang_masuk', 'barang_masuk.id', '=', 'barang_masuk_detail.barang_masuk_id')
            ->join('produk', 'produk.id', '=', 'barang_masuk_detail.produk_id')
            ->whereBetween('barang_masuk.tanggal', [$start, $end])
            ->where('barang_masuk.keterangan', '!=', '[VOID]')
            ->select(
                'produk.nama_barang', 
                \Illuminate\Support\Facades\DB::raw('SUM(barang_masuk_detail.qty_masuk) as total_qty'),
                \Illuminate\Support\Facades\DB::raw('SUM(barang_masuk_detail.qty_masuk * barang_masuk_detail.harga_beli) as subtotal')
            )
            ->groupBy('produk.id', 'produk.nama_barang')
            ->having('total_qty', '>', 0)
            ->orderByDesc('subtotal')
            ->get();

        // Rekap Audit Batal
        $batalPenjualan = \App\Models\Penjualan::whereBetween('tanggal', [$start, $end])->where('keterangan', 'like', '[VOID]%')->count();
        $batalMasuk = \App\Models\BarangMasuk::whereBetween('tanggal', [$start, $end])->where('keterangan', 'like', '[VOID]%')->count();

        // Total Aset Saat Ini
        $totalAssetValue = $this->valuationService->getTotalAssetValue();

        // 2. Render Pesan WA via Blade View
        $pesan = trim(view('whatsapp.tutup-shift', [
            'tanggal' => $todayStr,
            'pelapor' => Auth::user()->name,
            'totalNotaSales' => $totalNotaSales,
            'omzetHariIni' => $omzetHariIni,
            'batalPenjualan' => $batalPenjualan,
            'rincianPenjualan' => $rincianPenjualan,
            'totalNotaGM' => $totalNotaGM,
            'pengeluaranHariIni' => $pengeluaranHariIni,
            'batalMasuk' => $batalMasuk,
            'rincianBelanja' => $rincianBelanja,
            'totalAssetValue' => $totalAssetValue,
        ])->render());

        \Illuminate\Support\Facades\Log::info('Tentative WA Message:', ['pesan' => $pesan]);

        // 3. Kirim
        $sent = $this->fonnteService->sendMessage($ownerWa, $pesan);

        if ($sent) {
            return back()->with('success', 'Laporan Shift Detail berhasil dikirim ke WhatsApp Owner!');
        } else {
            return back()->with('error', 'Gagal mengirim pesan WhatsApp. Pastikan Fonnte Token Valid dan Server tidak Down.');
        }
    }
}
