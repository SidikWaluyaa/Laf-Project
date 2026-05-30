<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSatuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
            'keterangan' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_satuan.required' => 'Nama satuan harus diisi.',
            'nama_satuan.unique' => 'Nama satuan sudah ada.',
            'nama_satuan.max' => 'Nama satuan maksimal 50 karakter.',
        ];
    }
}
