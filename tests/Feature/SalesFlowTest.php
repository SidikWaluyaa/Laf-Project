<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\StokProduk;
use App\Services\StockService;
use App\Services\GoodsReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Produk $produk;
    private Lokasi $lokasi;
    private Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kategori = Kategori::create(['nama_kategori' => 'Test', 'kode_prefix' => 'TST']);
        $this->produk = Produk::create([
            'kode_barang' => 'LAF-TST-001',
            'nama_barang' => 'Produk Test',
            'kategori_id' => $kategori->id,
            'satuan' => 'PCS',
            'hpp' => 10000,
        ]);

        $this->lokasi = Lokasi::create(['nama_lokasi' => 'Gudang Test']);
        $this->pelanggan = Pelanggan::create(['nama_pelanggan' => 'Customer Test']);

        // Pre-load stock
        app(StockService::class)->increaseStok($this->produk->id, $this->lokasi->id, 50);
    }

    public function test_penjualan_reduces_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('penjualan.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'tipe_nota' => 'invoice',
            'pelanggan_id' => $this->pelanggan->id,
            'nomor_nota' => 'INV-001',
            'lokasi_id' => $this->lokasi->id,
            'keterangan' => 'Test sale',
            'details' => [
                [
                    'produk_id' => $this->produk->id,
                    'qty_keluar' => 5,
                ],
            ],
        ]);

        $response->assertRedirect(route('penjualan.index'));
        $response->assertSessionHas('success');

        // Stock should be reduced
        $stok = app(StockService::class)->getStok($this->produk->id, $this->lokasi->id);
        $this->assertEquals(45, $stok);
    }

    public function test_penjualan_fails_with_insufficient_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('penjualan.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'pelanggan_id' => $this->pelanggan->id,
            'nomor_nota' => 'INV-002',
            'lokasi_id' => $this->lokasi->id,
            'details' => [
                [
                    'produk_id' => $this->produk->id,
                    'qty_keluar' => 999,
                ],
            ],
        ]);

        $response->assertSessionHas('error');

        // Stock should remain unchanged
        $stok = app(StockService::class)->getStok($this->produk->id, $this->lokasi->id);
        $this->assertEquals(50, $stok);
    }

    public function test_penjualan_requires_at_least_one_detail(): void
    {
        $response = $this->actingAs($this->admin)->post(route('penjualan.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'pelanggan_id' => $this->pelanggan->id,
            'lokasi_id' => $this->lokasi->id,
            'details' => [],
        ]);

        $response->assertSessionHasErrors('details');
    }
}
