<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use App\Http\Requests\StoreStokMinimumRequest;
use App\Models\Produk;
use App\Models\StokMinimum;
use Illuminate\Http\Request;

class StokMinimumController extends Controller
{
    public function __construct(
        private StockService $stockService
    ) {}

    public function index()
    {
        $products = $this->stockService->getStokMinimumList();
        return view('stok-minimum.index', compact('products'));
    }

    public function store(StoreStokMinimumRequest $request)
    {
        $this->stockService->setStokMinimum($request->produk_id, $request->stok_minimum);
        return redirect()->route('stok-minimum.index')->with('success', 'Stok minimum berhasil diatur.');
    }

    public function create()
    {
        $produk = Produk::doesntHave('stokMinimum')->get();
        return view('stok-minimum.create', compact('produk'));
    }

    public function update(Request $request, int $produkId)
    {
        $request->validate(['stok_minimum' => 'required|integer|min:0']);
        $this->stockService->setStokMinimum($produkId, $request->stok_minimum);
        return back()->with('success', 'Stok minimum berhasil diperbarui.');
    }

    public function destroy(int $produkId)
    {
        StokMinimum::where('produk_id', $produkId)->delete();
        return back()->with('success', 'Stok minimum berhasil dihapus.');
    }
}
