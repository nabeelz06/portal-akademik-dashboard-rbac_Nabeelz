<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Sudah terisi? Lewati supaya db:seed aman dijalankan berulang
        if (Mahasiswa::exists()) {
            return;
        }

        $departemenIds = Departemen::pluck('id');
        $dosenPerDepartemen = Dosen::all()->groupBy('departemen_id');

        // 60 mahasiswa. Dosen wali dipilih dari departemen yang sama.
        Mahasiswa::factory()
            ->count(60)
            ->state(function () use ($departemenIds, $dosenPerDepartemen) {
                $departemenId = $departemenIds->random();

                return [
                    'departemen_id' => $departemenId,
                    'dosen_wali_id' => $dosenPerDepartemen->get($departemenId)?->random()->id,
                ];
            })
            ->create();
    }
}
