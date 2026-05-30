<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Http\Requests\StoreLokasiRequest;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::paginate(15);
        return view('lokasi.index', compact('lokasi'));
    }

    public function store(StoreLokasiRequest $request)
    {
        Lokasi::create($request->only('nama_lokasi'));
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(StoreLokasiRequest $request, Lokasi $lokasi)
    {
        $lokasi->update($request->only('nama_lokasi'));
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->stokProduk()->exists()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Lokasi tidak bisa dihapus karena masih memiliki stok produk.');
        }

        if ($lokasi->barangMasuk()->exists()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Lokasi tidak bisa dihapus karena masih memiliki riwayat barang masuk.');
        }

        if ($lokasi->penjualan()->exists()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Lokasi tidak bisa dihapus karena masih memiliki riwayat penjualan.');
        }

        $lokasi->delete();
        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
