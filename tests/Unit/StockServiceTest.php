<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\StockService;
use App\Models\StokProduk;
use App\Models\Produk;
use App\Models\Lokasi;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = app(StockService::class);
    }

    private function createProduk(): Produk
    {
        $kategori = Kategori::create(['nama_kategori' => 'Test', 'kode_prefix' => 'TST']);
        return Produk::create([
            'kode_barang' => 'LAF-TST-001',
            'nama_barang' => 'Produk Test',
            'kategori_id' => $kategori->id,
            'satuan' => 'PCS',
            'hpp' => 10000,
        ]);
    }

    private function createLokasi(): Lokasi
    {
        return Lokasi::create(['nama_lokasi' => 'Gudang Test']);
    }

    public function test_increase_stok_creates_record_if_not_exists(): void
    {
        $produk = $this->createProduk();
        $lokasi = $this->createLokasi();

        $this->stockService->increaseStok($produk->id, $lokasi->id, 10);

        $this->assertDatabaseHas('stok_produk', [
            'produk_id' => $produk->id,
            'lokasi_id' => $lokasi->id,
            'total_stok' => 10,
        ]);
    }

    public function test_increase_stok_increments_existing_record(): void
    {
        $produk = $this->createProduk();
        $lokasi = $this->createLokasi();

        $this->stockService->increaseStok($produk->id, $lokasi->id, 10);
        $this->stockService->increaseStok($produk->id, $lokasi->id, 5);

        $stok = $this->stockService->getStok($produk->id, $lokasi->id);
        $this->assertEquals(15, $stok);
    }

    public function test_decrease_stok_reduces_stock(): void
    {
        $produk = $this->createProduk();
        $lokasi = $this->createLokasi();

        $this->stockService->increaseStok($produk->id, $lokasi->id, 10);
        $this->stockService->decreaseStok($produk->id, $lokasi->id, 3);

        $stok = $this->stockService->getStok($produk->id, $lokasi->id);
        $this->assertEquals(7, $stok);
    }

    public function test_decrease_stok_throws_exception_when_insufficient(): void
    {
        $produk = $this->createProduk();
        $lokasi = $this->createLokasi();

        $this->stockService->increaseStok($produk->id, $lokasi->id, 5);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok tidak cukup');

        $this->stockService->decreaseStok($produk->id, $lokasi->id, 10);
    }

    public function test_decrease_stok_throws_exception_when_no_record(): void
    {
        $produk = $this->createProduk();
        $lokasi = $this->createLokasi();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok tidak cukup');

        $this->stockService->decreaseStok($produk->id, $lokasi->id, 1);
    }

    public function test_get_total_stok_sums_across_locations(): void
    {
        $produk = $this->createProduk();
        $lokasi1 = Lokasi::create(['nama_lokasi' => 'Gudang A']);
        $lokasi2 = Lokasi::create(['nama_lokasi' => 'Gudang B']);

        $this->stockService->increaseStok($produk->id, $lokasi1->id, 10);
        $this->stockService->increaseStok($produk->id, $lokasi2->id, 20);

        $total = $this->stockService->getTotalStok($produk->id);
        $this->assertEquals(30, $total);
    }

    public function test_set_stok_minimum_creates_and_updates(): void
    {
        $produk = $this->createProduk();

        $min = $this->stockService->setStokMinimum($produk->id, 5);
        $this->assertEquals(5, $min->stok_minimum);

        $min = $this->stockService->setStokMinimum($produk->id, 10);
        $this->assertEquals(10, $min->stok_minimum);

        $this->assertDatabaseCount('stok_minimum', 1);
    }
}
