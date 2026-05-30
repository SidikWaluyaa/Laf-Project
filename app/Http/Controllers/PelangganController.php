<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Http\Requests\StorePelangganRequest;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::paginate(15);
        return view('pelanggan.index', compact('pelanggan'));
    }

    public function store(StorePelangganRequest $request)
    {
        Pelanggan::create($request->only('nama_pelanggan'));
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(StorePelangganRequest $request, Pelanggan $pelanggan)
    {
        $pelanggan->update($request->only('nama_pelanggan'));
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->penjualan()->exists()) {
            return redirect()->route('pelanggan.index')
                ->with('error', 'Pelanggan tidak bisa dihapus karena masih memiliki riwayat penjualan.');
        }

        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
