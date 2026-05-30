<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Lokasi;
use Carbon\Carbon;

class DummyPenjualanSeeder extends Seeder
{
    public function run()
    {
        $produkIds = Produk::pluck('id')->toArray();
        $pelangganIds = Pelanggan::pluck('id')->toArray();
        $adminId = User::where('role', 'admin')->first()?->id ?? 1;
        $lokasiId = Lokasi::first()?->id ?? 1;

        if (count($produkIds) < 5) {
            $this->command->error('Harap isi minimal 5 produk master terlebih dahulu.');
            return;
        }

        $this->command->info('Memulai generate 50 data penjualan dummy...');

        for ($i = 1; $i <= 50; $i++) {
            $tanggal = Carbon::now()->subDays(rand(1, 30));
            
            $penjualan = Penjualan::create([
                'tanggal' => $tanggal,
                'tipe_nota' => 'cash',
                'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)] ?? null,
                'nomor_nota' => 'INV-' . $tanggal->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'admin_id' => $adminId,
                'lokasi_id' => $lokasiId,
                'keterangan' => 'Data Dummy untuk Analisis FP-Growth',
            ]);

            // Tentukan berapa banyak item dalam 1 struk (1-4 barang)
            $itemCount = rand(1, 4);
            $selectedProduks = [];

            // TRIK: Agar ada pola FP-Growth yang kuat, 
            // kita buat 40% transaksi mengandung pasangan produk tertentu
            if (rand(1, 10) <= 4) {
                // Pasangan wajib (Misal Produk ID ke-0 dan ke-1)
                $selectedProduks[] = $produkIds[0];
                $selectedProduks[] = $produkIds[1];
                $itemCount = max(2, $itemCount);
            }

            while (count($selectedProduks) < $itemCount) {
                $pId = $produkIds[array_rand($produkIds)];
                if (!in_array($pId, $selectedProduks)) {
                    $selectedProduks[] = $pId;
                }
            }

            foreach ($selectedProduks as $pId) {
                $produk = Produk::find($pId);
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $pId,
                    'qty_keluar' => rand(1, 3),
                    'hpp_snapshot' => $produk->hpp ?? 0,
                ]);
            }
        }

        $this->command->info('Berhasil menyuntikkan 50 transaksi baru ke database!');
    }
}
