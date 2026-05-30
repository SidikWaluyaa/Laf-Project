<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StokMinimumController;
use App\Http\Controllers\RiwayatGudangController;
use App\Http\Controllers\RiwayatTanggalController;
use App\Http\Controllers\RiwayatSupplierPelangganController;
use App\Http\Controllers\ArusBarangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\StokLokasiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/tutup-shift', [DashboardController::class, 'tutupShift'])->name('tutup-shift');

    // Master Produk
    Route::resource('produk', ProdukController::class);
    Route::get('produk-template/download', [ProdukController::class, 'downloadTemplate'])->name('produk.template');
    Route::post('produk-import', [ProdukController::class, 'import'])->name('produk.import');
    Route::post('produk-import/preview', [ProdukController::class, 'importPreview'])->name('produk.import.preview');
    Route::post('produk-import/confirm', [ProdukController::class, 'importConfirm'])->name('produk.import.confirm');

    // API: Next kode barang preview
    Route::get('/api/produk/next-code/{kategoriId}', function (int $kategoriId) {
        $service = app(\App\Services\ProductService::class);
        return response()->json(['kode' => $service->generateKodeBarang($kategoriId)]);
    })->name('api.produk.next-code');

    // Barang Masuk
    Route::resource('barang-masuk', BarangMasukController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('barang-masuk/{id}/void', [BarangMasukController::class, 'void'])->name('barang-masuk.void');
    Route::get('barang-masuk-riwayat', [BarangMasukController::class, 'riwayat'])->name('barang-masuk.riwayat');
    Route::get('barang-masuk-riwayat/pdf', [BarangMasukController::class, 'exportPdf'])->name('barang-masuk.riwayat.pdf');

    // Riwayat Gudang (Masuk & Keluar per Lokasi)
    Route::get('riwayat-gudang', [RiwayatGudangController::class, 'index'])->name('riwayat-gudang');
    Route::get('riwayat-gudang/pdf', [RiwayatGudangController::class, 'exportPdf'])->name('riwayat-gudang.pdf');

    // Riwayat Per Tanggal (Masuk & Keluar)
    Route::get('riwayat-tanggal', [RiwayatTanggalController::class, 'index'])->name('riwayat-tanggal');
    Route::get('riwayat-tanggal/pdf', [RiwayatTanggalController::class, 'exportPdf'])->name('riwayat-tanggal.pdf');

    // Riwayat Supplier & Pelanggan
    Route::get('riwayat-supplier-pelanggan', [RiwayatSupplierPelangganController::class, 'index'])->name('riwayat-supplier-pelanggan');
    Route::get('riwayat-supplier-pelanggan/pdf', [RiwayatSupplierPelangganController::class, 'exportPdf'])->name('riwayat-supplier-pelanggan.pdf');

    // Arus Barang
    Route::get('arus-barang', [ArusBarangController::class, 'index'])->name('arus-barang');
    Route::get('arus-barang/pdf', [ArusBarangController::class, 'exportPdf'])->name('arus-barang.pdf');

    // Penjualan
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('penjualan/{id}/void', [PenjualanController::class, 'void'])->name('penjualan.void');
    Route::get('penjualan-riwayat', [PenjualanController::class, 'riwayat'])->name('penjualan.riwayat');
    Route::get('penjualan-riwayat/pdf', [PenjualanController::class, 'exportPdf'])->name('penjualan.riwayat.pdf');

    // Purchase Order
    Route::resource('purchase-order', PurchaseOrderController::class);
    Route::get('purchase-order/{id}/pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-order.pdf');

    // Stok Minimum
    Route::get('stok-minimum', [StokMinimumController::class, 'index'])->name('stok-minimum.index');
    Route::get('stok-minimum/create', [StokMinimumController::class, 'create'])->name('stok-minimum.create');
    Route::post('stok-minimum', [StokMinimumController::class, 'store'])->name('stok-minimum.store');
    Route::put('stok-minimum/{produkId}', [StokMinimumController::class, 'update'])->name('stok-minimum.update');
    Route::delete('stok-minimum/{produkId}', [StokMinimumController::class, 'destroy'])->name('stok-minimum.destroy');

    // Stok per Lokasi
    Route::get('stok-lokasi', [StokLokasiController::class, 'index'])->name('stok-lokasi.index');

    // Laporan
    Route::get('laporan/nilai-aset', [LaporanController::class, 'nilaiAset'])->name('laporan.nilai-aset');
    Route::get('laporan/nilai-aset/pdf', [LaporanController::class, 'nilaiAsetPdf'])->name('laporan.nilai-aset.pdf');

    // FP-Growth Analysis
    Route::get('fp-growth', [\App\Http\Controllers\FpGrowthController::class, 'index'])->name('fp-growth.index');
    Route::post('fp-growth/process', [\App\Http\Controllers\FpGrowthController::class, 'process'])->name('fp-growth.process');

    // Master Data (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('lokasi', LokasiController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('supplier', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::post('satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
