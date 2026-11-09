<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TugasRequest extends FormRequest
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
        $tenggat = ['required', 'date'];

        // Tugas baru tidak boleh bertenggat di masa lalu. Saat ubah data
        // aturan ini dilewati supaya tugas lama tetap bisa diedit.
        if ($this->isMethod('POST')) {
            $tenggat[] = 'after_or_equal:today';
        }

        return [
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'departemen_id' => ['required', 'integer', 'exists:departemen,id'],
            'dosen_id' => ['nullable', 'integer', 'exists:dosen,id'],
            'tenggat' => $tenggat,
        ];
    }
}
