<?php

namespace App\Http\Controllers;

use App\Services\PurchaseOrderService;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private PurchaseOrderService $poService
    ) {}

    public function index(Request $request)
    {
        $purchaseOrders = $this->poService->list($request->search);
        return view('purchase-order.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::select('id', 'nama_supplier')->orderBy('nama_supplier')->get();
        $produk = Produk::select('id', 'kode_barang', 'nama_barang', 'variasi_barang', 'hpp')->orderBy('nama_barang')->get();
        $autoNomorPo = $this->poService->generateNomorPo();
        return view('purchase-order.create', compact('suppliers', 'produk', 'autoNomorPo'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        try {
            $data = $request->only(['tanggal', 'supplier_id', 'nomor_po', 'status']);
            $data['status'] = $data['status'] ?? 'draft';

            $this->poService->create($data, $request->details);

            return redirect()->route('purchase-order.index')->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $po = $this->poService->find($id);
        return view('purchase-order.show', compact('po'));
    }

    public function edit(int $id)
    {
        $po = $this->poService->find($id);
        $suppliers = Supplier::select('id', 'nama_supplier')->orderBy('nama_supplier')->get();
        $produk = Produk::select('id', 'kode_barang', 'nama_barang', 'variasi_barang', 'hpp')->orderBy('nama_barang')->get();
        return view('purchase-order.edit', compact('po', 'suppliers', 'produk'));
    }

    public function update(UpdatePurchaseOrderRequest $request, int $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);
            $data = $request->only(['tanggal', 'supplier_id', 'status']);

            $this->poService->update($po, $data, $request->details);

            return redirect()->route('purchase-order.index')->with('success', 'Purchase Order berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $po = PurchaseOrder::findOrFail($id);
            $this->poService->delete($po);
            return redirect()->route('purchase-order.index')->with('success', 'Purchase Order berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdf(int $id)
    {
        $po = $this->poService->find($id);

        $statusLabel = match($po->status) {
            'selesai' => 'Selesai',
            'sebagian' => 'Sebagian Diterima',
            'dikirim' => 'Dikirim',
            default => 'Draft',
        };

        $tanggalFormatted = \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y');
        $tanggalFile = \Carbon\Carbon::parse($po->tanggal)->format('Ymd');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.purchase-order', [
            'title' => 'Purchase Order — ' . ($po->supplier->nama_supplier ?? 'N/A'),
            'filterInfo' => "Tanggal: <span>{$tanggalFormatted}</span> &bull; Supplier: <span>{$po->supplier->nama_supplier}</span> &bull; Status: <span>{$statusLabel}</span>",
            'po' => $po,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("PO_{$po->id}_{$tanggalFile}.pdf");
    }
}
