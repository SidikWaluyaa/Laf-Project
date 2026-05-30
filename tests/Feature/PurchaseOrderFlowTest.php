<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Services\StockService;
use App\Services\GoodsReceiptService;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseOrderFlowTest extends TestCase
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

    public function test_po_can_be_created(): void
    {
        $response = $this->actingAs($this->admin)->post(route('purchase-order.store'), [
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
            'details' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 100,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchase-order.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_order', [
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
        ]);
    }

    public function test_po_lifecycle_draft_to_selesai(): void
    {
        $poService = app(PurchaseOrderService::class);
        $goodsReceiptService = app(GoodsReceiptService::class);

        // Create PO
        $po = $poService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'status' => 'draft'],
            [['produk_id' => $this->produk->id, 'jumlah' => 10]]
        );

        $this->assertEquals('draft', $po->status);
        $this->assertEquals(10, $po->detail->first()->sisa);

        // Receive partial goods
        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 5, 'harga_beli' => 10000]]
        );

        $po->refresh();
        $this->assertEquals('sebagian', $po->status);

        // Receive remaining goods
        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 5, 'harga_beli' => 10000]]
        );

        $po->refresh();
        $this->assertEquals('selesai', $po->status);

        // Stock should be 10
        $stok = app(StockService::class)->getStok($this->produk->id, $this->lokasi->id);
        $this->assertEquals(10, $stok);
    }

    public function test_po_cannot_be_edited_after_receiving(): void
    {
        $poService = app(PurchaseOrderService::class);
        $goodsReceiptService = app(GoodsReceiptService::class);

        // Create PO with detail
        $po = $poService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'status' => 'draft'],
            [['produk_id' => $this->produk->id, 'jumlah' => 10]]
        );

        // Receive some goods
        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 3, 'harga_beli' => 10000]]
        );

        // Trying to edit should fail
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PO tidak bisa diedit');

        $poService->update(
            $po,
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'status' => 'draft'],
            [['produk_id' => $this->produk->id, 'jumlah' => 20]]
        );
    }

    public function test_po_cannot_be_deleted_after_receiving(): void
    {
        $poService = app(PurchaseOrderService::class);
        $goodsReceiptService = app(GoodsReceiptService::class);

        $po = $poService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'status' => 'draft'],
            [['produk_id' => $this->produk->id, 'jumlah' => 10]]
        );

        $goodsReceiptService->create(
            ['tanggal' => now(), 'supplier_id' => $this->supplier->id, 'lokasi_id' => $this->lokasi->id, 'admin_id' => $this->admin->id],
            [['produk_id' => $this->produk->id, 'qty_masuk' => 3, 'harga_beli' => 10000]]
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PO tidak bisa dihapus');

        $poService->delete($po);
    }
}
