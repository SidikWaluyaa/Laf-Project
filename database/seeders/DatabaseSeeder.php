<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\StokProduk;
use App\Models\StokMinimum;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Services\ProductService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $productService = app(ProductService::class);

        // ─── Users ───────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin LAF',
            'email' => 'admin@laf.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Owner LAF',
            'email' => 'owner@laf.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        // ─── Kategori (with kode_prefix) ────────────────
        $sandalGunung  = Kategori::create(['nama_kategori' => 'Sandal Gunung',   'kode_prefix' => 'SG']);
        $sandalSantai  = Kategori::create(['nama_kategori' => 'Sandal Santai',   'kode_prefix' => 'SS']);
        $sepatu        = Kategori::create(['nama_kategori' => 'Sepatu',          'kode_prefix' => 'SP']);
        $aksesoris     = Kategori::create(['nama_kategori' => 'Aksesoris',       'kode_prefix' => 'AK']);
        $spareParts    = Kategori::create(['nama_kategori' => 'Spare Parts',     'kode_prefix' => 'SPR']);

        // ─── Lokasi ─────────────────────────────────────
        $toko    = Lokasi::create(['nama_lokasi' => 'TOKO CIBADUYUT']);
        $gudang  = Lokasi::create(['nama_lokasi' => 'GUDANG PRODUKSI']);
        $online  = Lokasi::create(['nama_lokasi' => 'GUDANG ONLINE']);

        // ─── Supplier ───────────────────────────────────
        $sup1 = Supplier::create(['nama_supplier' => 'CV Kulit Jaya Cibaduyut']);
        $sup2 = Supplier::create(['nama_supplier' => 'UD Sole Mandiri']);
        $sup3 = Supplier::create(['nama_supplier' => 'PT Bahan Tekstil Bandung']);
        $sup4 = Supplier::create(['nama_supplier' => 'CV Benang Emas']);
        $sup5 = Supplier::create(['nama_supplier' => 'UD Aksesoris Lengkap']);

        // ─── Pelanggan ──────────────────────────────────
        $plg1 = Pelanggan::create(['nama_pelanggan' => 'Toko Sepatu Pasar Baru']);
        $plg2 = Pelanggan::create(['nama_pelanggan' => 'Reseller Bandung']);
        $plg3 = Pelanggan::create(['nama_pelanggan' => 'Toko Outdoor Dago']);
        $plg4 = Pelanggan::create(['nama_pelanggan' => 'Walk-in Customer']);
        $plg5 = Pelanggan::create(['nama_pelanggan' => 'Marketplace (Shopee/Tokopedia)']);
        $plg6 = Pelanggan::create(['nama_pelanggan' => 'Reseller Jakarta']);

        // ─── Produk (kode_barang auto-generated!) ───────
        $produkData = [
            // Sandal Gunung → LAF-SG-001, LAF-SG-002, ...
            ['nama_barang' => 'LAF Summit X1',       'variasi_barang' => 'Black/Yellow - 40',  'kategori_id' => $sandalGunung->id, 'satuan' => 'PASANG', 'hpp' => 95000],
            ['nama_barang' => 'LAF Summit X1',       'variasi_barang' => 'Black/Yellow - 42',  'kategori_id' => $sandalGunung->id, 'satuan' => 'PASANG', 'hpp' => 95000],
            ['nama_barang' => 'LAF Trailblazer V2',  'variasi_barang' => 'Army Green - 41',    'kategori_id' => $sandalGunung->id, 'satuan' => 'PASANG', 'hpp' => 110000],
            ['nama_barang' => 'LAF Trailblazer V2',  'variasi_barang' => 'Army Green - 43',    'kategori_id' => $sandalGunung->id, 'satuan' => 'PASANG', 'hpp' => 110000],
            ['nama_barang' => 'LAF Explorer Pro',    'variasi_barang' => 'Black/Red - 42',     'kategori_id' => $sandalGunung->id, 'satuan' => 'PASANG', 'hpp' => 125000],

            // Sandal Santai → LAF-SS-001, LAF-SS-002, ...
            ['nama_barang' => 'LAF Chill Slide',     'variasi_barang' => 'Full Black - 40',    'kategori_id' => $sandalSantai->id, 'satuan' => 'PASANG', 'hpp' => 45000],
            ['nama_barang' => 'LAF Chill Slide',     'variasi_barang' => 'Full Black - 42',    'kategori_id' => $sandalSantai->id, 'satuan' => 'PASANG', 'hpp' => 45000],
            ['nama_barang' => 'LAF Urban Flip',      'variasi_barang' => 'Navy/White - 41',    'kategori_id' => $sandalSantai->id, 'satuan' => 'PASANG', 'hpp' => 55000],
            ['nama_barang' => 'LAF Comfort Max',     'variasi_barang' => 'Grey - 43',          'kategori_id' => $sandalSantai->id, 'satuan' => 'PASANG', 'hpp' => 65000],

            // Sepatu → LAF-SP-001, LAF-SP-002, ...
            ['nama_barang' => 'LAF Street Runner',   'variasi_barang' => 'Full Black - 41',    'kategori_id' => $sepatu->id, 'satuan' => 'PASANG', 'hpp' => 175000],
            ['nama_barang' => 'LAF Street Runner',   'variasi_barang' => 'Black/White - 42',   'kategori_id' => $sepatu->id, 'satuan' => 'PASANG', 'hpp' => 175000],
            ['nama_barang' => 'LAF Trekking Boot',   'variasi_barang' => 'Brown - 42',         'kategori_id' => $sepatu->id, 'satuan' => 'PASANG', 'hpp' => 220000],
            ['nama_barang' => 'LAF Daily Loafer',    'variasi_barang' => 'Tan - 40',           'kategori_id' => $sepatu->id, 'satuan' => 'PASANG', 'hpp' => 145000],

            // Aksesoris → LAF-AK-001, LAF-AK-002
            ['nama_barang' => 'Tali Sandal Gunung',  'variasi_barang' => 'Universal',          'kategori_id' => $aksesoris->id, 'satuan' => 'PCS', 'hpp' => 15000],
            ['nama_barang' => 'Insole Premium',      'variasi_barang' => 'All Size',           'kategori_id' => $aksesoris->id, 'satuan' => 'PASANG', 'hpp' => 20000],

            // Spare Parts → LAF-SPR-001, LAF-SPR-002
            ['nama_barang' => 'Sole Karet Outdoor',  'variasi_barang' => '42',                 'kategori_id' => $spareParts->id, 'satuan' => 'PASANG', 'hpp' => 35000],
            ['nama_barang' => 'Buckle Stainless',    'variasi_barang' => null,                 'kategori_id' => $spareParts->id, 'satuan' => 'PCS', 'hpp' => 8000],
        ];

        $allProduk = [];
        foreach ($produkData as $data) {
            // Gunakan ProductService untuk auto-generate kode_barang
            $produk = $productService->create($data);
            $allProduk[] = $produk;

            // Stok per lokasi
            StokProduk::create(['produk_id' => $produk->id, 'lokasi_id' => $toko->id,   'total_stok' => rand(5, 30)]);
            StokProduk::create(['produk_id' => $produk->id, 'lokasi_id' => $gudang->id, 'total_stok' => rand(20, 80)]);
            StokProduk::create(['produk_id' => $produk->id, 'lokasi_id' => $online->id, 'total_stok' => rand(10, 50)]);

            // Stok minimum
            StokMinimum::create([
                'produk_id' => $produk->id,
                'stok_minimum' => rand(10, 25),
            ]);
        }

        // ─── Barang Masuk (Sample Transactions) ─────────
        $bm1 = BarangMasuk::create([
            'tanggal' => '2026-02-20',
            'supplier_id' => $sup1->id,
            'admin_id' => $admin->id,
            'lokasi_id' => $gudang->id,
            'keterangan' => 'Restok bahan kulit untuk sandal gunung',
        ]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm1->id, 'produk_id' => $allProduk[0]->id, 'qty_masuk' => 50, 'harga_beli' => 90000]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm1->id, 'produk_id' => $allProduk[2]->id, 'qty_masuk' => 30, 'harga_beli' => 105000]);

        $bm2 = BarangMasuk::create([
            'tanggal' => '2026-02-22',
            'supplier_id' => $sup2->id,
            'admin_id' => $admin->id,
            'lokasi_id' => $gudang->id,
            'keterangan' => 'Restok sole karet dan sandal santai',
        ]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm2->id, 'produk_id' => $allProduk[5]->id, 'qty_masuk' => 60, 'harga_beli' => 42000]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm2->id, 'produk_id' => $allProduk[15]->id, 'qty_masuk' => 100, 'harga_beli' => 33000]);

        $bm3 = BarangMasuk::create([
            'tanggal' => '2026-02-25',
            'supplier_id' => $sup3->id,
            'admin_id' => $admin->id,
            'lokasi_id' => $online->id,
            'keterangan' => 'Pindah stok ke gudang online',
        ]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm3->id, 'produk_id' => $allProduk[9]->id, 'qty_masuk' => 25, 'harga_beli' => 170000]);
        BarangMasukDetail::create(['barang_masuk_id' => $bm3->id, 'produk_id' => $allProduk[10]->id, 'qty_masuk' => 25, 'harga_beli' => 170000]);

        // ─── Penjualan (Sample Transactions) ────────────
        $pj1 = Penjualan::create([
            'tanggal' => '2026-02-21',
            'tipe_nota' => 'Cash',
            'pelanggan_id' => $plg4->id,
            'nomor_nota' => 'INV-20260221-001',
            'admin_id' => $admin->id,
            'lokasi_id' => $toko->id,
            'keterangan' => 'Penjualan langsung di toko',
        ]);
        PenjualanDetail::create(['penjualan_id' => $pj1->id, 'produk_id' => $allProduk[0]->id, 'qty_keluar' => 2, 'hpp_snapshot' => 95000]);
        PenjualanDetail::create(['penjualan_id' => $pj1->id, 'produk_id' => $allProduk[7]->id, 'qty_keluar' => 1, 'hpp_snapshot' => 55000]);

        $pj2 = Penjualan::create([
            'tanggal' => '2026-02-23',
            'tipe_nota' => 'Transfer',
            'pelanggan_id' => $plg5->id,
            'nomor_nota' => 'INV-20260223-001',
            'admin_id' => $admin->id,
            'lokasi_id' => $online->id,
            'keterangan' => 'Pesanan Shopee batch',
        ]);
        PenjualanDetail::create(['penjualan_id' => $pj2->id, 'produk_id' => $allProduk[5]->id, 'qty_keluar' => 5, 'hpp_snapshot' => 45000]);
        PenjualanDetail::create(['penjualan_id' => $pj2->id, 'produk_id' => $allProduk[9]->id, 'qty_keluar' => 3, 'hpp_snapshot' => 175000]);
        PenjualanDetail::create(['penjualan_id' => $pj2->id, 'produk_id' => $allProduk[13]->id, 'qty_keluar' => 4, 'hpp_snapshot' => 15000]);

        // ─── Purchase Order ─────────────────────────────
        $po1 = PurchaseOrder::create([
            'tanggal' => '2026-02-26',
            'supplier_id' => $sup1->id,
            'status' => 'dikirim',
        ]);
        PurchaseOrderDetail::create(['purchase_order_id' => $po1->id, 'produk_id' => $allProduk[4]->id, 'jumlah' => 100, 'barang_masuk' => 0, 'sisa' => 100]);
        PurchaseOrderDetail::create(['purchase_order_id' => $po1->id, 'produk_id' => $allProduk[11]->id, 'jumlah' => 50, 'barang_masuk' => 0, 'sisa' => 50]);

        $po2 = PurchaseOrder::create([
            'tanggal' => '2026-02-27',
            'supplier_id' => $sup2->id,
            'status' => 'draft',
        ]);
        PurchaseOrderDetail::create(['purchase_order_id' => $po2->id, 'produk_id' => $allProduk[15]->id, 'jumlah' => 200, 'barang_masuk' => 0, 'sisa' => 200]);
        PurchaseOrderDetail::create(['purchase_order_id' => $po2->id, 'produk_id' => $allProduk[8]->id, 'jumlah' => 80, 'barang_masuk' => 0, 'sisa' => 80]);

        $po3 = PurchaseOrder::create([
            'tanggal' => '2026-02-15',
            'supplier_id' => $sup3->id,
            'status' => 'sebagian',
        ]);
        PurchaseOrderDetail::create(['purchase_order_id' => $po3->id, 'produk_id' => $allProduk[9]->id, 'jumlah' => 60, 'barang_masuk' => 25, 'sisa' => 35]);
    }
}
