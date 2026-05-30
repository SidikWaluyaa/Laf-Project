<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:supplier,id',
            'lokasi_id' => 'required|exists:lokasi,id',
            'nomor_nota' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.qty_masuk' => 'required|integer|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Minimal satu item barang harus diisi.',
            'details.*.produk_id.required' => 'Produk harus dipilih.',
            'details.*.qty_masuk.required' => 'Jumlah masuk harus diisi.',
            'details.*.qty_masuk.min' => 'Jumlah masuk minimal 1.',
            'details.*.harga_beli.required' => 'Harga beli harus diisi.',
        ];
    }
}
