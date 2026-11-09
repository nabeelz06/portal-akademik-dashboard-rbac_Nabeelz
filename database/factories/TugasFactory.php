<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    public function definition(): array
    {
        $jenis = fake()->randomElement([
            'Laporan Praktikum',
            'Tugas Mandiri',
            'Proyek Kelompok',
            'Review Jurnal',
            'Presentasi',
            'Kuis Daring',
        ]);
        $matkul = fake()->randomElement([
            'PBKK',
            'Basis Data',
            'Jaringan Komputer',
            'Kecerdasan Buatan',
            'Struktur Data',
            'Sistem Operasi',
            'Analisis Algoritma',
            'Interaksi Manusia dan Komputer',
        ]);
        $bagian = fake()->randomElement(['Modul', 'Pertemuan']).' '.fake()->numberBetween(1, 14);

        return [
            'judul' => "{$jenis} {$matkul} {$bagian}",
            'deskripsi' => fake()->randomElement([
                'Kerjakan secara individu dan kumpulkan melalui LMS ITS sebelum tenggat.',
                'Sertakan laporan singkat dan tautan repositori GitHub pada berkas pengumpulan.',
                'Dikerjakan berkelompok. Setiap anggota wajib mencatat kontribusinya.',
                'Jawaban harus orisinal. Kemiripan tinggi dengan pekerjaan lain akan dikurangi nilainya.',
                'Presentasi singkat maksimal 5 menit pada pertemuan berikutnya.',
            ]),
            'departemen_id' => Departemen::factory(),
            'dosen_id' => null,
            // Sebagian sudah lewat tenggat, sebagian masih aktif
            'tenggat' => fake()->dateTimeBetween('-10 days', '+45 days')->format('Y-m-d'),
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn () => [
            'tenggat' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
        ]);
    }

    public function lewatTenggat(): static
    {
        return $this->state(fn () => [
            'tenggat' => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);
    }
}
