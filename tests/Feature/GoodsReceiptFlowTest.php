<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Services\StockService;
use App\Services\GoodsReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoodsReceiptFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Produk $produk;
    private Supplier $supplier;
    private Lokasi $lokasi;

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

        $this->supplier = Supplier::create(['nama_supplier' => 'Supplier Test']);
        $this->lokasi = Lokasi::create(['nama_lokasi' => 'Gudang Test']);
    }

    public function test_barang_masuk_increases_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barang-masuk.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'lokasi_id' => $this->lokasi->id,
            'keterangan' => 'Test receipt',
            'details' => [
                [
                    'produk_id' => $this->produk->id,
                    'qty_masuk' => 20,
                    'harga_beli' => 12000,
                ],
            ],
        ]);

        $response->assertRedirect(route('barang-masuk.index'));
        $response->assertSessionHas('success');

        $stok = app(StockService::class)->getStok($this->produk->id, $this->lokasi->id);
        $this->assertEquals(20, $stok);
    }

    public function test_barang_masuk_updates_hpp_weighted_average(): void
    {
        $goodsReceiptService = app(GoodsReceiptService::class);

        // First receipt: 10 units @ 10000 = total value 100000
        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 10, 'harga_beli' => 10000]]
        );

        // Second receipt: 10 units @ 12000 = total value 120000
        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 10, 'harga_beli' => 12000]]
        );

        // Weighted average HPP = (100000 + 120000) / 20 = 11000
        $this->produk->refresh();
        $this->assertEquals(11000, (float) $this->produk->hpp);
    }

    public function test_barang_masuk_requires_valid_details(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barang-masuk.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'lokasi_id' => $this->lokasi->id,
            'details' => [],
        ]);

        $response->assertSessionHasErrors('details');
    }
}
