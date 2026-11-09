<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidasiFormTest extends TestCase
{
    use RefreshDatabase;

    private User $staf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siapkanPeran();
        $this->staf = $this->buatPengguna('staf');
    }

    private function dataMahasiswa(array $timpa = []): array
    {
        return array_merge([
            'nrp' => '5025241024',
            'nama' => 'Muhammad Nabil Fauzan',
            'email' => 'nabil@student.its.ac.id',
            'departemen_id' => Departemen::factory()->create()->id,
            'angkatan' => 2024,
            'ipk' => '3.75',
            'is_active' => '1',
        ], $timpa);
    }

    public function test_mahasiswa_valid_tersimpan(): void
    {
        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), $this->dataMahasiswa())
            ->assertRedirect(route('admin.mahasiswa.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mahasiswa', ['nrp' => '5025241024', 'nama' => 'Muhammad Nabil Fauzan']);
    }

    public function test_nrp_harus_10_digit_angka(): void
    {
        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), $this->dataMahasiswa(['nrp' => '12345']))
            ->assertSessionHasErrors('nrp');

        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), $this->dataMahasiswa(['nrp' => 'abcdefghij']))
            ->assertSessionHasErrors('nrp');

        $this->assertDatabaseCount('mahasiswa', 0);
    }

    public function test_nrp_tidak_boleh_kembar(): void
    {
        Mahasiswa::factory()->create(['nrp' => '5025241024']);

        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), $this->dataMahasiswa())
            ->assertSessionHasErrors('nrp');
    }

    public function test_ubah_data_boleh_memakai_nrp_milik_sendiri(): void
    {
        $mahasiswa = Mahasiswa::factory()->create(['nrp' => '5025241024']);

        $this->actingAs($this->staf)
            ->put(route('admin.mahasiswa.update', $mahasiswa), $this->dataMahasiswa([
                'departemen_id' => $mahasiswa->departemen_id,
                'email' => $mahasiswa->email,
                'nama' => 'Nama Baru',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.mahasiswa.index'));

        $this->assertDatabaseHas('mahasiswa', ['id' => $mahasiswa->id, 'nama' => 'Nama Baru']);
    }

    public function test_ipk_dan_angkatan_dibatasi(): void
    {
        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), $this->dataMahasiswa(['ipk' => '4.5', 'angkatan' => 1999]))
            ->assertSessionHasErrors(['ipk', 'angkatan']);
    }

    public function test_pesan_galat_berbahasa_indonesia(): void
    {
        $this->actingAs($this->staf)
            ->post(route('admin.mahasiswa.store'), [])
            ->assertSessionHasErrors(['nrp' => 'NRP wajib diisi.']);
    }

    public function test_tugas_baru_tidak_boleh_bertenggat_masa_lalu(): void
    {
        $departemen = Departemen::factory()->create();

        $this->actingAs($this->staf)
            ->post(route('admin.tugas.store'), [
                'judul' => 'Tugas Terlambat',
                'departemen_id' => $departemen->id,
                'tenggat' => now()->subDay()->toDateString(),
            ])
            ->assertSessionHasErrors('tenggat');
    }

    public function test_tugas_lama_yang_lewat_tenggat_tetap_bisa_diubah(): void
    {
        $tugas = Tugas::factory()->lewatTenggat()->create();

        $this->actingAs($this->staf)
            ->put(route('admin.tugas.update', $tugas), [
                'judul' => 'Judul Diperbarui',
                'departemen_id' => $tugas->departemen_id,
                'tenggat' => $tugas->tenggat->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tugas', ['id' => $tugas->id, 'judul' => 'Judul Diperbarui']);
    }

    public function test_form_tambah_dan_ubah_bisa_dirender(): void
    {
        $mahasiswa = Mahasiswa::factory()->create();
        $tugas = Tugas::factory()->create();

        foreach ([
            route('admin.mahasiswa.create'),
            route('admin.mahasiswa.edit', $mahasiswa),
            route('admin.dosen.create'),
            route('admin.tugas.create'),
            route('admin.tugas.edit', $tugas),
        ] as $url) {
            $this->actingAs($this->staf)->get($url)->assertOk();
        }

        $this->actingAs($this->staf)
            ->get(route('admin.mahasiswa.edit', $mahasiswa))
            ->assertSee($mahasiswa->nrp)
            ->assertSee($mahasiswa->nama);
    }
}
