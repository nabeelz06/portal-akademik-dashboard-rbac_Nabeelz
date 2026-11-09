<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login Instan (Auto-Login) untuk Demo
    |--------------------------------------------------------------------------
    |
    | Jika aktif, halaman login menampilkan tombol untuk masuk langsung sebagai
    | akun uji coba. Fitur ini hanya untuk demo dan evaluasi di kelas. Pada
    | lingkungan produksi, isi DEMO_LOGIN=false pada berkas .env.
    |
    */

    'enabled' => (bool) env('DEMO_LOGIN', false) && env('APP_ENV') !== 'production',

    'password' => env('DEMO_PASSWORD', 'password'),

    'accounts' => [
        'admin' => [
            'name' => 'Admin Akademik',
            'email' => 'admin@akademik.test',
            'role' => 'admin',
            'label' => 'Admin',
            'hint' => 'Akses penuh, termasuk hapus data',
            'icon' => 'fa-user-shield',
            'button' => 'btn-primary',
        ],
        'staf' => [
            'name' => 'Staf Akademik',
            'email' => 'staf@akademik.test',
            'role' => 'staf',
            'label' => 'Staf',
            'hint' => 'Akses terbatas, tanpa hapus data',
            'icon' => 'fa-user-tie',
            'button' => 'btn-info',
        ],
        'pengguna' => [
            'name' => 'Pengguna Biasa',
            'email' => 'pengguna@akademik.test',
            'role' => null,
            'label' => 'Pengguna Biasa',
            'hint' => 'Tanpa peran, hanya beranda publik',
            'icon' => 'fa-user',
            'button' => 'btn-secondary',
        ],
    ],

];
