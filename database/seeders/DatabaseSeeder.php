<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting: peran dan akun dulu, baru data akademik.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            DepartemenSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
            TugasSeeder::class,
        ]);
    }
}
