<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MahasiswaRequest extends FormRequest
{
    /**
     * Otorisasi sudah ditangani middleware permission di controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        // Null saat tambah data, model saat ubah data (dipakai untuk abaikan baris sendiri)
        $mahasiswa = $this->route('mahasiswa');

        return [
            'nrp' => ['required', 'digits:10', Rule::unique('mahasiswa', 'nrp')->ignore($mahasiswa)],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('mahasiswa', 'email')->ignore($mahasiswa)],
            'departemen_id' => ['required', 'integer', 'exists:departemen,id'],
            'dosen_wali_id' => ['nullable', 'integer', 'exists:dosen,id'],
            'angkatan' => ['required', 'integer', 'between:2015,'.now()->year],
            'ipk' => ['required', 'numeric', 'between:0,4'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
