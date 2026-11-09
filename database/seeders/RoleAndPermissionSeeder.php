<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Modul yang punya aksi lihat, tambah, ubah, dan hapus.
     */
    private const MODUL = ['mahasiswa', 'dosen', 'tugas'];

    private const AKSI = ['lihat', 'tambah', 'ubah', 'hapus'];

    public function run(): void
    {
        // Kosongkan cache permission supaya perubahan langsung terbaca
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $semua = ['lihat-dashboard', 'kelola-pengguna'];

        foreach (self::MODUL as $modul) {
            foreach (self::AKSI as $aksi) {
                $semua[] = "{$aksi}-{$modul}";
            }
        }

        foreach ($semua as $nama) {
            Permission::firstOrCreate(['name' => $nama, 'guard_name' => 'web']);
        }

        // Admin: akses penuh, termasuk hapus data dan kelola pengguna
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($semua);

        // Staf: lihat, tambah, dan ubah saja. Tidak boleh menghapus data
        // dan tidak boleh mengelola pengguna.
        $staf = Role::firstOrCreate(['name' => 'staf', 'guard_name' => 'web']);
        $staf->syncPermissions(
            array_filter(
                $semua,
                fn (string $nama) => ! str_starts_with($nama, 'hapus-') && $nama !== 'kelola-pengguna'
            )
        );
    }
}
