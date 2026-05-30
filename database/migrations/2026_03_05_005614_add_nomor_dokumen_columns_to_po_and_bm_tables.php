<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->string('nomor_po')->nullable()->after('supplier_id');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->string('nomor_nota')->nullable()->after('lokasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn('nomor_po');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('nomor_nota');
        });
    }
};
