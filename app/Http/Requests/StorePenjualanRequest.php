<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'tipe_nota' => 'nullable|string|max:50',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'nomor_nota' => 'nullable|string|max:100',
            'lokasi_id' => 'required|exists:lokasi,id',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.qty_keluar' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Minimal satu item barang harus diisi.',
            'details.*.produk_id.required' => 'Produk harus dipilih.',
            'details.*.qty_keluar.required' => 'Jumlah keluar harus diisi.',
            'details.*.qty_keluar.min' => 'Jumlah keluar minimal 1.',
        ];
    }
}
