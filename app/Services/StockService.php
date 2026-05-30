<?php

namespace App\Services;

use App\Models\StokProduk;
use App\Models\StokMinimum;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\FonnteService;

class StockService
{
    public function __construct(private ?FonnteService $fonnteService = null) {}

    /**
     * Get stok for a specific produk at a specific lokasi.
     */
    public function getStok(int $produkId, int $lokasiId): int
    {
        $stok = StokProduk::where('produk_id', $produkId)
            ->where('lokasi_id', $lokasiId)
            ->first();

        return $stok ? $stok->total_stok : 0;
    }

    /**
     * Get total stok across all lokasi for a produk.
     */
    public function getTotalStok(int $produkId): int
    {
        return StokProduk::where('produk_id', $produkId)->sum('total_stok');
    }

    /**
     * Increase stok for a produk at a lokasi.
     */
    public function increaseStok(int $produkId, int $lokasiId, int $qty): void
    {
        $stok = StokProduk::firstOrCreate(
            ['produk_id' => $produkId, 'lokasi_id' => $lokasiId],
            ['total_stok' => 0]
        );

        $stok->increment('total_stok', $qty);
    }

    /**
     * Decrease stok for a produk at a lokasi.
     * Uses pessimistic locking to prevent race conditions.
     * Throws exception if insufficient stock.
     */
    public function decreaseStok(int $produkId, int $lokasiId, int $qty): void
    {
        $stok = StokProduk::where('produk_id', $produkId)
            ->where('lokasi_id', $lokasiId)
            ->lockForUpdate()
            ->first();

        if (!$stok || $stok->total_stok < $qty) {
            $produk = Produk::find($produkId);
            throw new \Exception("Stok tidak cukup untuk produk {$produk->nama_barang}. Tersedia: " . ($stok ? $stok->total_stok : 0) . ", dibutuhkan: {$qty}");
        }

        // --- Logic Peringatan Stok Minimum ---
        $totalStokSebelum = $this->getTotalStok($produkId);
        $totalStokSesudah = $totalStokSebelum - $qty;
        
        $produk = Produk::with('stokMinimum')->find($produkId);
        $batasMinimum = $produk->stokMinimum->stok_minimum ?? 0;

        $stok->decrement('total_stok', $qty);

        // Kirim notifikasi HANYA jika stok turun melewati batas untuk pertama kalinya
        if ($batasMinimum > 0 && $totalStokSebelum > $batasMinimum && $totalStokSesudah <= $batasMinimum) {
            $ownerWa = config('app.wa_target_owner', env('WA_TARGET_OWNER', ''));
            if (!empty($ownerWa)) {
                $service = $this->fonnteService ?? app(FonnteService::class);
                $pesan = "🚨 *PERINGATAN STOK MENIPIS* 🚨\n\n";
                $pesan .= "Barang: *{$produk->nama_barang}*\n";
                $pesan .= "Sisa Stok Keseluruhan: *{$totalStokSesudah}* (Batas Min: {$batasMinimum})\n\n";
                $pesan .= "_Harap segera lakukan cek atau restock/PO ke Supplier._";
                
                $service->sendMessage($ownerWa, $pesan);
            }
        }
    }

    /**
     * Get total stok across all products.
     */
    public function getTotalAllStok(): int
    {
        return StokProduk::sum('total_stok');
    }

    /**
     * Get products that are below minimum stock.
     * Uses SQL subquery instead of loading all products to memory.
     */
    public function getLowStockProducts(): Collection
    {
        return Produk::with(['kategori', 'stokMinimum'])
            ->select('produk.*')
            ->selectSub(
                StokProduk::selectRaw('COALESCE(SUM(total_stok), 0)')
                    ->whereColumn('stok_produk.produk_id', 'produk.id'),
                'computed_total_stok'
            )
            ->whereHas('stokMinimum')
            ->havingRaw('computed_total_stok <= (SELECT stok_minimum FROM stok_minimum WHERE stok_minimum.produk_id = produk.id)')
            ->get()
            ->values();
    }

    /**
     * Get all products with stok minimum info.
     * Uses SQL subquery instead of loading all products to memory.
     */
    public function getStokMinimumList(): Collection
    {
        return Produk::with(['stokProduk', 'stokMinimum', 'kategori'])
            ->select('produk.*')
            ->selectSub(
                StokProduk::selectRaw('COALESCE(SUM(total_stok), 0)')
                    ->whereColumn('stok_produk.produk_id', 'produk.id'),
                'total_stok_all'
            )
            ->whereHas('stokMinimum')
            ->get()
            ->map(function (Produk $produk) {
                $produk->setAttribute('total_stok_all', (int) $produk->total_stok_all);
                $produk->setAttribute('is_low', $produk->total_stok_all <= ($produk->stokMinimum->stok_minimum ?? 0));
                return $produk;
            });
    }

    /**
     * Set stok minimum for a produk.
     */
    public function setStokMinimum(int $produkId, int $minimum): StokMinimum
    {
        return StokMinimum::updateOrCreate(
            ['produk_id' => $produkId],
            ['stok_minimum' => $minimum]
        );
    }
}
