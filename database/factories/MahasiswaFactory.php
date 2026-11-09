<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        $angkatan = fake()->numberBetween(2021, 2025);

        // Pola NRP: kode prodi (4 digit), angkatan (2 digit), urutan (4 digit)
        $nrp = fake()->randomElement(['5022', '5023', '5024', '5025', '5026', '5027'])
            .substr((string) $angkatan, 2)
            .fake()->unique()->numerify('####');

        return [
            'nrp' => $nrp,
            'nama' => fake()->firstName().' '.fake()->lastName(),
            'email' => $nrp.'@student.its.ac.id',
            'departemen_id' => Departemen::factory(),
            'dosen_wali_id' => null,
            'angkatan' => $angkatan,
            'ipk' => fake()->randomFloat(2, 2.4, 4.0),
            'is_active' => fake()->boolean(88),
        ];
    }

    public function cuti(): static
    {
        return $this->state(['is_active' => false]);
    }
}
