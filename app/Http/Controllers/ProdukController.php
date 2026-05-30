<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Satuan;
use App\Exports\ProdukTemplateExport;
use App\Imports\ProdukImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $produk = $this->productService->list($request->search);
        return view('produk.index', compact('produk'));
    }

    public function create()
    {
        $kategori = Kategori::select('id', 'nama_kategori', 'kode_prefix')->orderBy('nama_kategori')->get();
        $satuanList = Satuan::select('id', 'nama_satuan')->orderBy('nama_satuan')->get();
        return view('produk.create', compact('kategori', 'satuanList'));
    }

    public function store(StoreProdukRequest $request)
    {
        $this->productService->create($request->validated());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $produk = $this->productService->find($id);
        $mutasi = $this->productService->getMutasiStok($id);
        return view('produk.show', compact('produk', 'mutasi'));
    }

    public function edit(int $id)
    {
        $produk = $this->productService->find($id);
        $kategori = Kategori::select('id', 'nama_kategori', 'kode_prefix')->orderBy('nama_kategori')->get();
        $satuanList = Satuan::select('id', 'nama_satuan')->orderBy('nama_satuan')->get();
        return view('produk.edit', compact('produk', 'kategori', 'satuanList'));
    }

    public function update(UpdateProdukRequest $request, int $id)
    {
        $produk = Produk::findOrFail($id);
        $this->productService->update($produk, $request->validated());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $produk = Produk::findOrFail($id);
        $this->productService->delete($produk);
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Download Excel template for bulk import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ProdukTemplateExport(), 'Template_Import_Produk_LAF.xlsx');
    }

    /**
     * Preview products from Excel file before saving.
     */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new ProdukImport(previewMode: true);
            Excel::import($import, $request->file('file'));

            $validData = $import->getValidData();
            $errors = $import->getErrors();

            return view('produk.import-preview', compact('validData', 'errors'));
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Confirm and save imported products.
     */
    public function importConfirm(Request $request)
    {
        $request->validate([
            'data' => 'required|json',
        ]);

        $data = json_decode($request->data, true);
        $imported = 0;

        try {
            foreach ($data as $item) {
                $this->productService->create([
                    'kategori_id' => $item['kategori_id'],
                    'nama_barang' => $item['nama_barang'],
                    'variasi_barang' => $item['variasi_barang'] ?? null,
                    'satuan' => $item['satuan'],
                    'hpp' => $item['hpp'],
                ]);
                $imported++;
            }

            \App\Services\ActivityLogService::log('import', null, "Import {$imported} produk dari Excel");

            return redirect()->route('produk.index')
                ->with('success', "{$imported} produk berhasil diimport!");
        } catch (\Exception $e) {
            return redirect()->route('produk.index')
                ->with('error', 'Gagal menyimpan data import: ' . $e->getMessage());
        }
    }
}
