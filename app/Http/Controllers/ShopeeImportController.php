<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShopeeImportService;
use App\Models\PenjualanShopee;
use App\Models\PenjualanShopeeDetail;
use Illuminate\Support\Facades\DB;

class ShopeeImportController extends Controller
{
    public function index(Request $request)
    {
        $totalOrders = PenjualanShopee::count();
        $totalDetails = PenjualanShopeeDetail::count();
        $totalSelesai = PenjualanShopee::where('status_pesanan', 'Selesai')->count();
        $latestOrder = PenjualanShopee::max('waktu_pesanan_dibuat');

        // Count multi-item orders (>= 2 pure product items)
        $totalMultiItemOrders = PenjualanShopee::whereHas('detail', function($q) {
            $q->where('nama_produk', 'not like', '%bubble%')
              ->where('nama_produk', 'not like', '%kardus%')
              ->where('nama_produk', 'not like', '%packing%');
        }, '>=', 2)->count();

        $query = PenjualanShopee::with('detail');

        // Filter Basket Tab
        $filterBasket = $request->input('filter_basket', 'all');
        if ($filterBasket === 'multi_item') {
            $query->whereHas('detail', function($q) {
                $q->where('nama_produk', 'not like', '%bubble%')
                  ->where('nama_produk', 'not like', '%kardus%')
                  ->where('nama_produk', 'not like', '%packing%');
            }, '>=', 2);
        } elseif ($filterBasket === 'repeat_customer') {
            $repeatUsernames = PenjualanShopee::select('username_pembeli')
                ->whereNotNull('username_pembeli')
                ->where('username_pembeli', '!=', '')
                ->groupBy('username_pembeli')
                ->havingRaw('COUNT(id) >= 2')
                ->pluck('username_pembeli');

            $query->whereIn('username_pembeli', $repeatUsernames);
        }

        // Search Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('username_pembeli', 'like', "%{$search}%")
                  ->orWhereHas('detail', function($qd) use ($search) {
                      $qd->where('nama_produk', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status_pesanan', $request->status);
        }

        $orders = $query->orderBy('waktu_pesanan_dibuat', 'desc')->paginate(15)->withQueryString();

        // Unique Months available for selective deletion
        $availableMonths = PenjualanShopee::select(DB::raw("DATE_FORMAT(waktu_pesanan_dibuat, '%Y-%m') as month_year"))
            ->distinct()
            ->orderBy('month_year', 'desc')
            ->pluck('month_year');

        return view('penjualan.import-shopee', [
            'title' => 'Import & Data Transaksi Shopee',
            'totalOrders' => $totalOrders,
            'totalDetails' => $totalDetails,
            'totalSelesai' => $totalSelesai,
            'totalMultiItemOrders' => $totalMultiItemOrders,
            'latestOrder' => $latestOrder,
            'orders' => $orders,
            'filterBasket' => $filterBasket,
            'availableMonths' => $availableMonths,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        try {
            $file = $request->file('file_excel');
            $service = new ShopeeImportService();
            $result = $service->import($file->getRealPath());

            return back()->with('success', "Berhasil meng-import {$result['total_orders']} pesanan transaksi Shopee ({$result['total_details']} item detail produk).");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function destroyMassal(Request $request)
    {
        $request->validate([
            'mode_hapus' => 'required|in:all,month',
            'bulan_tahun' => 'nullable|string',
        ]);

        try {
            if ($request->mode_hapus === 'all') {
                $count = PenjualanShopee::count();
                PenjualanShopee::query()->delete();
                return back()->with('success', "Berhasil menghapus SELURUH data transaksi Shopee ({$count} pesanan).");
            } else {
                if (empty($request->bulan_tahun)) {
                    return back()->with('error', 'Silakan pilih bulan transaksi yang ingin dihapus.');
                }

                $query = PenjualanShopee::whereRaw("DATE_FORMAT(waktu_pesanan_dibuat, '%Y-%m') = ?", [$request->bulan_tahun]);
                $count = $query->count();
                $query->delete();

                return back()->with('success', "Berhasil menghapus {$count} pesanan transaksi Shopee untuk periode {$request->bulan_tahun}.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data transaksi: ' . $e->getMessage());
        }
    }
}
