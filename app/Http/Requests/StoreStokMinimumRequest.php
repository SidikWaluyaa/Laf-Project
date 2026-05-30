<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokMinimumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produk_id' => 'required|exists:produk,id',
            'stok_minimum' => 'required|integer|min:0',
        ];
    }
}
