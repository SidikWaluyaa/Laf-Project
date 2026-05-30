<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang' => 'required|string|max:50|unique:produk,kode_barang,' . $this->route('produk'),
            'nama_barang' => 'required|string|max:255',
            'variasi_barang' => 'nullable|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id' => 'required|exists:satuan,id',
            'hpp' => 'required|numeric|min:0',
        ];
    }
}
