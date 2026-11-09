<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Tugas;
use Illuminate\Database\Seeder;

class TugasSeeder extends Seeder
{
    public function run(): void
    {
        // Sudah terisi? Lewati supaya db:seed aman dijalankan berulang
        if (Tugas::exists()) {
            return;
        }

        $departemenIds = Departemen::pluck('id');
        $dosenPerDepartemen = Dosen::all()->groupBy('departemen_id');

        // 36 tugas. Pemberi tugas adalah dosen dari departemen yang sama.
        Tugas::factory()
            ->count(36)
            ->state(function () use ($departemenIds, $dosenPerDepartemen) {
                $departemenId = $departemenIds->random();

                return [
                    'departemen_id' => $departemenId,
                    'dosen_id' => $dosenPerDepartemen->get($departemenId)?->random()->id,
                ];
            })
            ->create();
    }
}
