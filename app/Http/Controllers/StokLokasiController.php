<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Produk;
use Illuminate\Http\Request;

class StokLokasiController extends Controller
{
    public function index(Request $request)
    {
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();
        $selectedLokasiId = $request->query('lokasi_id');
        
        $produk = null;
        if ($selectedLokasiId) {
            // Load products with their stock ONLY for the selected location
            $produk = Produk::with(['kategori', 'stokProduk' => function($q) use ($selectedLokasiId) {
                $q->where('lokasi_id', $selectedLokasiId);
            }])
            ->when($request->search, function ($query) use ($request) {
                $query->where('nama_barang', 'like', "%{$request->search}%")
                      ->orWhere('kode_barang', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
        }

        return view('stok-lokasi.index', compact('lokasiList', 'selectedLokasiId', 'produk'));
    }
}
