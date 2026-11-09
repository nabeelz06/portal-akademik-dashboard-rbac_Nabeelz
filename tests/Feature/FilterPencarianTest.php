<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji pencarian dan filter berbasis when() beserta withQueryString().
 */
class FilterPencarianTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siapkanPeran();
        $this->admin = $this->buatPengguna('admin');
    }

    private function bukaMahasiswa(array $query = [])
    {
        return $this->actingAs($this->admin)->get(route('admin.mahasiswa.index', $query));
    }

    public function test_tanpa_filter_semua_data_tampil(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Alya Rahma']);
        Mahasiswa::factory()->create(['nama' => 'Bima Santoso']);

        $this->bukaMahasiswa()->assertOk()->assertSee('Alya Rahma')->assertSee('Bima Santoso');
    }

    public function test_cari_berdasarkan_nama(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Nabil Fauzan']);
        Mahasiswa::factory()->create(['nama' => 'Budi Santoso']);

        $this->bukaMahasiswa(['q' => 'nabil'])
            ->assertOk()
            ->assertSee('Nabil Fauzan')
            ->assertDontSee('Budi Santoso');
    }

    public function test_cari_berdasarkan_nrp(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Nabil Fauzan', 'nrp' => '5025241024']);
        Mahasiswa::factory()->create(['nama' => 'Budi Santoso', 'nrp' => '5025249999']);

        $this->bukaMahasiswa(['q' => '5025241024'])
            ->assertOk()
            ->assertSee('Nabil Fauzan')
            ->assertDontSee('Budi Santoso');
    }

    public function test_filter_departemen(): void
    {
        $informatika = Departemen::factory()->create();
        $elektro = Departemen::factory()->create();

        Mahasiswa::factory()->create(['nama' => 'Anak Informatika', 'departemen_id' => $informatika->id]);
        Mahasiswa::factory()->create(['nama' => 'Anak Elektro', 'departemen_id' => $elektro->id]);

        $this->bukaMahasiswa(['departemen_id' => $informatika->id])
            ->assertSee('Anak Informatika')
            ->assertDontSee('Anak Elektro');
    }

    public function test_filter_status_aktif_dan_nonaktif(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Mahasiswa Aktif', 'is_active' => true]);
        Mahasiswa::factory()->create(['nama' => 'Mahasiswa Cuti', 'is_active' => false]);

        $this->bukaMahasiswa(['is_active' => 1])
            ->assertSee('Mahasiswa Aktif')
            ->assertDontSee('Mahasiswa Cuti');

        // Nilai "0" juga harus diterapkan sebagai filter, bukan dianggap kosong
        $this->bukaMahasiswa(['is_active' => 0])
            ->assertSee('Mahasiswa Cuti')
            ->assertDontSee('Mahasiswa Aktif');
    }

    public function test_filter_angkatan(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Angkatan Dua Empat', 'angkatan' => 2024]);
        Mahasiswa::factory()->create(['nama' => 'Angkatan Dua Tiga', 'angkatan' => 2023]);

        $this->bukaMahasiswa(['angkatan' => 2024])
            ->assertSee('Angkatan Dua Empat')
            ->assertDontSee('Angkatan Dua Tiga');
    }

    public function test_semua_filter_bisa_digabung(): void
    {
        $informatika = Departemen::factory()->create();
        $elektro = Departemen::factory()->create();

        Mahasiswa::factory()->create([
            'nama' => 'Cocok Semua', 'departemen_id' => $informatika->id, 'is_active' => true,
        ]);
        Mahasiswa::factory()->create([
            'nama' => 'Cocok Nama Saja', 'departemen_id' => $elektro->id, 'is_active' => true,
        ]);
        Mahasiswa::factory()->create([
            'nama' => 'Cocok Departemen Saja', 'departemen_id' => $informatika->id, 'is_active' => false,
        ]);

        $this->bukaMahasiswa([
            'q' => 'Cocok',
            'departemen_id' => $informatika->id,
            'is_active' => 1,
        ])
            ->assertSee('Cocok Semua')
            ->assertDontSee('Cocok Nama Saja')
            ->assertDontSee('Cocok Departemen Saja');
    }

    public function test_pencarian_tanpa_hasil_menampilkan_pesan_kosong(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Nabil Fauzan']);

        $this->bukaMahasiswa(['q' => 'tidak-ada-nama-ini'])
            ->assertOk()
            ->assertSee('Tidak ada mahasiswa yang cocok');
    }

    public function test_filter_terbawa_saat_pindah_halaman(): void
    {
        $departemen = Departemen::factory()->create();

        Mahasiswa::factory()->count(15)->create([
            'nama' => 'Uji Paginasi',
            'departemen_id' => $departemen->id,
        ]);
        Mahasiswa::factory()->count(3)->create(['nama' => 'Lainnya']);

        $halamanSatu = $this->bukaMahasiswa(['q' => 'Uji', 'departemen_id' => $departemen->id])
            ->assertOk()
            ->assertViewHas('mahasiswa', fn ($data) => $data->count() === 10 && $data->total() === 15);

        // Tautan halaman 2 wajib membawa q dan departemen_id (withQueryString)
        $this->assertMatchesRegularExpression(
            '/href="[^"]*q=Uji[^"]*departemen_id='.$departemen->id.'[^"]*page=2[^"]*"/',
            $halamanSatu->getContent()
        );

        $this->bukaMahasiswa(['q' => 'Uji', 'departemen_id' => $departemen->id, 'page' => 2])
            ->assertViewHas('mahasiswa', fn ($data) => $data->count() === 5 && $data->total() === 15);
    }

    public function test_input_tidak_wajar_tidak_membuat_error(): void
    {
        Mahasiswa::factory()->create();

        $this->bukaMahasiswa(['q' => ['x'], 'departemen_id' => 'abc', 'angkatan' => 'zzz', 'is_active' => ['1']])
            ->assertOk();
    }

    public function test_filter_dosen(): void
    {
        $departemen = Departemen::factory()->create();

        Dosen::factory()->create([
            'nama' => 'Dosen Lektor Satu', 'jabatan' => 'Lektor', 'departemen_id' => $departemen->id,
        ]);
        Dosen::factory()->create(['nama' => 'Dosen Profesor Dua', 'jabatan' => 'Profesor']);

        $this->actingAs($this->admin)
            ->get(route('admin.dosen.index', ['jabatan' => 'Lektor']))
            ->assertSee('Dosen Lektor Satu')
            ->assertDontSee('Dosen Profesor Dua');

        $this->actingAs($this->admin)
            ->get(route('admin.dosen.index', ['q' => 'Profesor Dua']))
            ->assertSee('Dosen Profesor Dua')
            ->assertDontSee('Dosen Lektor Satu');

        // Jabatan di luar daftar diabaikan, bukan error
        $this->actingAs($this->admin)
            ->get(route('admin.dosen.index', ['jabatan' => 'Rektor']))
            ->assertOk()
            ->assertSee('Dosen Lektor Satu');
    }

    public function test_filter_tugas_berdasarkan_judul_dan_status(): void
    {
        Tugas::factory()->aktif()->create(['judul' => 'Tugas Masih Aktif']);
        Tugas::factory()->lewatTenggat()->create(['judul' => 'Tugas Sudah Lewat']);

        $this->actingAs($this->admin)
            ->get(route('admin.tugas.index', ['status' => 'aktif']))
            ->assertSee('Tugas Masih Aktif')
            ->assertDontSee('Tugas Sudah Lewat');

        $this->actingAs($this->admin)
            ->get(route('admin.tugas.index', ['status' => 'lewat']))
            ->assertSee('Tugas Sudah Lewat')
            ->assertDontSee('Tugas Masih Aktif');

        $this->actingAs($this->admin)
            ->get(route('admin.tugas.index', ['q' => 'Lewat']))
            ->assertSee('Tugas Sudah Lewat')
            ->assertDontSee('Tugas Masih Aktif');
    }

    public function test_filter_pengguna(): void
    {
        $staf = $this->buatPengguna('staf');
        $biasa = $this->buatPengguna();

        $this->actingAs($this->admin)
            ->get(route('admin.pengguna.index', ['peran' => 'staf']))
            ->assertSee($staf->email)
            ->assertDontSee($biasa->email);

        $this->actingAs($this->admin)
            ->get(route('admin.pengguna.index', ['peran' => 'tanpa']))
            ->assertSee($biasa->email)
            ->assertDontSee($staf->email);

        // Peran yang tidak ada diabaikan, bukan error
        $this->actingAs($this->admin)
            ->get(route('admin.pengguna.index', ['peran' => 'superuser']))
            ->assertOk();
    }
}
