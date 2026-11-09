<?php

/*
| Pesan validasi berbahasa Indonesia (aturan yang dipakai aplikasi ini).
| Aturan lain otomatis memakai bahasa Inggris dari bawaan Laravel.
*/

return [

    'accepted' => ':Attribute harus diterima.',
    'after_or_equal' => ':Attribute harus berupa tanggal setelah atau sama dengan :date.',
    'between' => [
        'array' => ':Attribute harus terdiri dari :min sampai :max item.',
        'file' => ':Attribute harus berukuran antara :min sampai :max kilobita.',
        'numeric' => ':Attribute harus bernilai antara :min dan :max.',
        'string' => ':Attribute harus terdiri dari :min sampai :max karakter.',
    ],
    'boolean' => 'Isian :attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'digits' => ':Attribute harus terdiri dari :digits digit angka.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'lowercase' => ':Attribute harus berupa huruf kecil.',
    'max' => [
        'array' => ':Attribute tidak boleh lebih dari :max item.',
        'file' => ':Attribute tidak boleh lebih dari :max kilobita.',
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'array' => ':Attribute minimal terdiri dari :min item.',
        'file' => ':Attribute minimal berukuran :min kilobita.',
        'numeric' => ':Attribute minimal bernilai :min.',
        'string' => ':Attribute minimal terdiri dari :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus mengandung setidaknya satu huruf.',
        'mixed' => ':Attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => ':Attribute harus mengandung setidaknya satu angka.',
        'symbols' => ':Attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => ':Attribute yang diberikan pernah bocor di internet. Pilih :attribute yang lain.',
    ],
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',

    'custom' => [],

    'attributes' => [
        'name' => 'nama',
        'nama' => 'nama',
        'email' => 'email',
        'password' => 'kata sandi',
        'current_password' => 'kata sandi saat ini',
        'password_confirmation' => 'konfirmasi kata sandi',
        'nrp' => 'NRP',
        'nip' => 'NIP',
        'departemen_id' => 'departemen',
        'dosen_id' => 'dosen',
        'dosen_wali_id' => 'dosen wali',
        'angkatan' => 'angkatan',
        'ipk' => 'IPK',
        'is_active' => 'status',
        'jabatan' => 'jabatan',
        'judul' => 'judul',
        'deskripsi' => 'deskripsi',
        'tenggat' => 'tenggat',
        'peran' => 'peran',
    ],

];
