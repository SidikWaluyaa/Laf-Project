<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Http\Requests\StoreSatuanRequest;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $satuanList = Satuan::withCount('produk')
            ->when($search, fn($q) => $q->where('nama_satuan', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%"))
            ->orderBy('nama_satuan')
            ->paginate(15)
            ->appends($request->all());

        return view('satuan.index', compact('satuanList'));
    }

    public function store(StoreSatuanRequest $request)
    {
        Satuan::create([
            'nama_satuan' => strtoupper(trim($request->nama_satuan)),
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Satuan berhasil ditambahkan!');
    }

    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan,' . $satuan->id,
            'keterangan' => 'nullable|string|max:100',
        ]);

        $satuan->update([
            'nama_satuan' => strtoupper(trim($request->nama_satuan)),
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Satuan berhasil diperbarui!');
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->produk()->exists()) {
            return back()->with('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh ' . $satuan->produk()->count() . ' produk.');
        }

        $satuan->delete();
        return back()->with('success', 'Satuan berhasil dihapus!');
    }
}
