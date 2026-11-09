<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siapkanPeran();
    }

    public function test_widget_menampilkan_angka_dari_database(): void
    {
        $departemen = Departemen::factory()->create();

        Mahasiswa::factory()->count(3)->create(['departemen_id' => $departemen->id, 'is_active' => true]);
        Mahasiswa::factory()->count(2)->create(['departemen_id' => $departemen->id, 'is_active' => false]);
        Dosen::factory()->count(4)->create(['departemen_id' => $departemen->id]);
        Tugas::factory()->aktif()->count(6)->create(['departemen_id' => $departemen->id]);
        Tugas::factory()->lewatTenggat()->count(2)->create(['departemen_id' => $departemen->id]);

        $this->actingAs($this->buatPengguna('admin'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahMahasiswa', 5)
            ->assertViewHas('jumlahMahasiswaAktif', 3)
            ->assertViewHas('jumlahDosen', 4)
            ->assertViewHas('jumlahTugasAktif', 6);
    }

    public function test_widget_ikut_berubah_saat_data_berubah(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('jumlahMahasiswa', 0)
            ->assertViewHas('jumlahDosen', 0);

        Mahasiswa::factory()->count(2)->create();
        Dosen::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('jumlahMahasiswa', 2)
            ->assertViewHas('jumlahDosen', 1);
    }

    public function test_dashboard_kosong_tidak_error(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada mahasiswa')
            ->assertSee('Tidak ada tugas aktif');
    }

    public function test_widget_pengguna_hanya_untuk_admin(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Pengguna Terdaftar');

        $this->actingAs($this->buatPengguna('admin'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pengguna Terdaftar');
    }
}
