<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dosen>
 */
class DosenFactory extends Factory
{
    public function definition(): array
    {
        $nama = fake()->firstName().' '.fake()->lastName();
        $gelarDepan = fake()->randomElement(['', '', 'Dr.', 'Dr. Eng.', 'Prof. Dr.']);
        $gelarBelakang = fake()->randomElement([
            'S.Kom., M.Kom.',
            'S.T., M.T.',
            'S.Kom., M.Sc.',
            'S.Si., M.Sc.',
            'S.T., M.Eng., Ph.D.',
        ]);

        // Pola NIP: tanggal lahir (8 digit), TMT (6 digit), jenis kelamin (1), urutan (3)
        $lahir = fake()->dateTimeBetween('-60 years', '-32 years');
        $tmt = fake()->dateTimeBetween('-25 years', '-3 years');
        $nip = $lahir->format('Ymd')
            .$tmt->format('Ym')
            .fake()->numberBetween(1, 2)
            .fake()->unique()->numerify('###');

        return [
            'nip' => $nip,
            'nama' => trim($gelarDepan.' '.$nama).', '.$gelarBelakang,
            'email' => fake()->unique()->userName().'@its.ac.id',
            'departemen_id' => Departemen::factory(),
            'jabatan' => fake()->randomElement(Dosen::JABATAN),
        ];
    }
}
