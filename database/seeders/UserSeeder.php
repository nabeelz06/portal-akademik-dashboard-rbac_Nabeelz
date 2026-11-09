<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Buat akun uji coba (daftar akun ada di config/demo.php).
     * Aman dijalankan berulang: akun yang sudah ada tidak dibuat ulang.
     */
    public function run(): void
    {
        foreach (config('demo.accounts') as $akun) {
            $user = User::where('email', $akun['email'])->first()
                ?? User::factory()->create([
                    'name' => $akun['name'],
                    'email' => $akun['email'],
                    'password' => config('demo.password'),
                ]);

            // Akun "Pengguna Biasa" sengaja tanpa peran
            $user->syncRoles($akun['role'] ? [$akun['role']] : []);
        }
    }
}
