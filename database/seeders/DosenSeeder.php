<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        // Sudah terisi? Lewati supaya db:seed aman dijalankan berulang
        if (Dosen::exists()) {
            return;
        }

        $departemenIds = Departemen::pluck('id')->all();

        // 24 dosen, dibagi rata ke semua departemen
        Dosen::factory()
            ->count(24)
            ->sequence(fn (Sequence $urutan) => [
                'departemen_id' => $departemenIds[$urutan->index % count($departemenIds)],
            ])
            ->create();
    }
}
