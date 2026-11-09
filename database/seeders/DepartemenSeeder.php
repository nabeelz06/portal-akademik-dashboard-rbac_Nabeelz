<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            'IF' => 'Teknik Informatika',
            'SI' => 'Sistem Informasi',
            'TI' => 'Teknologi Informasi',
            'TK' => 'Teknik Komputer',
            'TE' => 'Teknik Elektro',
            'BM' => 'Teknik Biomedik',
        ];

        foreach ($daftar as $kode => $nama) {
            Departemen::firstOrCreate(['kode' => $kode], ['nama' => $nama]);
        }
    }
}
