<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(15);
        return view('supplier.index', compact('suppliers'));
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->only('nama_supplier'));
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->only('nama_supplier'));
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->barangMasuk()->exists()) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki riwayat barang masuk.');
        }

        if ($supplier->purchaseOrder()->exists()) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak bisa dihapus karena masih memiliki purchase order.');
        }

        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
