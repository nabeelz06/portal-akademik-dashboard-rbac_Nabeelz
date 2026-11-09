<?php

namespace App\Http\Requests;

use App\Models\Dosen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DosenRequest extends FormRequest
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
        $dosen = $this->route('dosen');

        return [
            'nip' => ['required', 'digits:18', Rule::unique('dosen', 'nip')->ignore($dosen)],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', Rule::unique('dosen', 'email')->ignore($dosen)],
            'departemen_id' => ['required', 'integer', 'exists:departemen,id'],
            'jabatan' => ['nullable', Rule::in(Dosen::JABATAN)],
        ];
    }
}
