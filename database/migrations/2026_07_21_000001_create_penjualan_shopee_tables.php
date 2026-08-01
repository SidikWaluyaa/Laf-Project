<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_shopee', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan', 100)->unique();
            $table->string('tipe_pesanan', 100)->nullable();
            $table->string('status_pesanan', 50)->index();
            $table->text('alasan_pembatalan')->nullable();
            $table->string('status_pembatalan', 100)->nullable();
            $table->string('no_resi', 100)->nullable();
            $table->string('opsi_pengiriman', 100)->nullable();
            $table->string('metode_pengiriman', 50)->nullable();
            $table->dateTime('deadline_pengiriman')->nullable();
            $table->dateTime('waktu_pengiriman_diatur')->nullable();
            $table->dateTime('waktu_pesanan_dibuat')->index();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->string('metode_pembayaran', 100)->nullable();
            $table->decimal('voucher_penjual', 15, 2)->default(0);
            $table->decimal('cashback_koin', 15, 2)->default(0);
            $table->decimal('voucher_shopee', 15, 2)->default(0);
            $table->decimal('potongan_koin', 15, 2)->default(0);
            $table->decimal('diskon_kartu_kredit', 15, 2)->default(0);
            $table->decimal('ongkir_pembeli', 15, 2)->default(0);
            $table->decimal('estimasi_potongan_ongkir', 15, 2)->default(0);
            $table->decimal('ongkir_pengembalian', 15, 2)->default(0);
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            $table->decimal('perkiraan_ongkir', 15, 2)->default(0);
            $table->text('catatan_pembeli')->nullable();
            $table->text('catatan')->nullable();
            $table->string('username_pembeli', 100)->nullable();
            $table->string('nama_penerima', 150)->nullable();
            $table->string('no_telepon', 50)->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->dateTime('waktu_pesanan_selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('penjualan_shopee_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_shopee_id')->constrained('penjualan_shopee')->cascadeOnDelete();
            $table->string('sku_induk', 100)->nullable();
            $table->string('nama_produk', 255)->index();
            $table->string('nomor_referensi_sku', 100)->nullable();
            $table->string('nama_variasi', 150)->nullable();
            $table->decimal('harga_awal', 15, 2)->default(0);
            $table->decimal('harga_setelah_diskon', 15, 2)->default(0);
            $table->integer('jumlah')->default(0);
            $table->integer('returned_quantity')->default(0);
            $table->decimal('subtotal_pesanan', 15, 2)->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('diskon_penjual', 15, 2)->default(0);
            $table->decimal('diskon_shopee', 15, 2)->default(0);
            $table->string('berat_produk', 50)->nullable();
            $table->integer('jumlah_produk_dipesan')->default(0);
            $table->string('total_berat', 50)->nullable();
            $table->string('paket_diskon', 10)->nullable();
            $table->decimal('paket_diskon_shopee', 15, 2)->default(0);
            $table->decimal('paket_diskon_penjual', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_shopee_detail');
        Schema::dropIfExists('penjualan_shopee');
    }
};
