<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Isi tabel roles dan permissions (Admin dan Staf).
     */
    protected function siapkanPeran(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Buat pengguna dengan peran tertentu. Peran null berarti pengguna biasa.
     * Panggil siapkanPeran() lebih dulu jika memakai peran.
     */
    protected function buatPengguna(?string $peran = null): User
    {
        $user = User::factory()->create();

        if ($peran !== null) {
            $user->assignRole($peran);
        }

        return $user;
    }
}
