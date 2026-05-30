<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:255',
            'kode_prefix' => 'required|string|max:5|alpha|unique:kategori,kode_prefix,' . $this->route('kategori')->id,
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'kode_prefix.required' => 'Kode prefix harus diisi.',
            'kode_prefix.unique' => 'Kode prefix sudah digunakan.',
            'kode_prefix.alpha' => 'Kode prefix hanya boleh berisi huruf.',
            'kode_prefix.max' => 'Kode prefix maksimal 5 karakter.',
        ];
    }
}
