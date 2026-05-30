<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
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
            'nomor_po' => 'nullable|string',
            'status' => 'nullable|in:draft,dikirim,sebagian,selesai',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ];
    }
}
