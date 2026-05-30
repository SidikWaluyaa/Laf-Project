<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lokasi' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lokasi.required' => 'Nama lokasi harus diisi.',
        ];
    }
}
