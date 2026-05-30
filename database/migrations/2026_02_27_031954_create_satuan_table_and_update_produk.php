<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create satuan table
        Schema::create('satuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan', 50)->unique();
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();
        });

        // 2. Seed default satuan values
        $satuanList = [
            ['nama_satuan' => 'PASANG', 'keterangan' => 'Pasang (sepatu/sandal)'],
            ['nama_satuan' => 'PCS', 'keterangan' => 'Pieces / Satuan'],
            ['nama_satuan' => 'SET', 'keterangan' => 'Set (paket lengkap)'],
            ['nama_satuan' => 'BOX', 'keterangan' => 'Box / Kotak'],
            ['nama_satuan' => 'LUSIN', 'keterangan' => 'Lusin (12 pcs)'],
            ['nama_satuan' => 'PACK', 'keterangan' => 'Pack / Kemasan'],
            ['nama_satuan' => 'ROLL', 'keterangan' => 'Roll / Gulungan'],
            ['nama_satuan' => 'METER', 'keterangan' => 'Meter'],
            ['nama_satuan' => 'LEMBAR', 'keterangan' => 'Lembar'],
            ['nama_satuan' => 'KG', 'keterangan' => 'Kilogram'],
        ];

        foreach ($satuanList as $s) {
            DB::table('satuan')->insert(array_merge($s, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Add satuan_id to produk table
        Schema::table('produk', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable()->after('kategori_id')
                  ->constrained('satuan')->nullOnDelete();
        });

        // 4. Migrate existing text data → satuan_id
        $pasangId = DB::table('satuan')->where('nama_satuan', 'PASANG')->value('id');
        if ($pasangId) {
            DB::table('produk')->whereNotNull('satuan')->update(['satuan_id' => $pasangId]);
        }

        // 5. Optionally map any other existing text values
        $existingValues = DB::table('produk')->select('satuan')->distinct()->pluck('satuan');
        foreach ($existingValues as $val) {
            if (!$val) continue;
            $upper = strtoupper(trim($val));
            $satuanRow = DB::table('satuan')->where('nama_satuan', $upper)->first();
            if ($satuanRow) {
                DB::table('produk')->where('satuan', $val)->update(['satuan_id' => $satuanRow->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn('satuan_id');
        });
        Schema::dropIfExists('satuan');
    }
};
