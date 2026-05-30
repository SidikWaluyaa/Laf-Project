<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
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
            'status' => 'nullable|in:draft,dikirim,sebagian,selesai',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Minimal satu item barang harus diisi.',
            'details.*.produk_id.required' => 'Produk harus dipilih.',
            'details.*.jumlah.required' => 'Jumlah harus diisi.',
            'details.*.jumlah.min' => 'Jumlah minimal 1.',
        ];
    }
}
