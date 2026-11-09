<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siapkanPeran();
    }

    public function test_matriks_hak_akses_sesuai_rancangan(): void
    {
        $admin = Role::findByName('admin');
        $staf = Role::findByName('staf');

        foreach (['mahasiswa', 'dosen', 'tugas'] as $modul) {
            $this->assertTrue($admin->hasPermissionTo("hapus-{$modul}"));
            $this->assertFalse($staf->hasPermissionTo("hapus-{$modul}"));

            foreach (['lihat', 'tambah', 'ubah'] as $aksi) {
                $this->assertTrue($admin->hasPermissionTo("{$aksi}-{$modul}"));
                $this->assertTrue($staf->hasPermissionTo("{$aksi}-{$modul}"));
            }
        }

        $this->assertTrue($admin->hasPermissionTo('kelola-pengguna'));
        $this->assertFalse($staf->hasPermissionTo('kelola-pengguna'));
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.mahasiswa.index'))->assertRedirect(route('login'));
        $this->delete(route('admin.tugas.destroy', 1))->assertRedirect(route('login'));
    }

    public function test_pengguna_tanpa_peran_ditolak_di_seluruh_panel(): void
    {
        $pengguna = $this->buatPengguna();

        $rute = [
            'admin.dashboard',
            'admin.mahasiswa.index',
            'admin.dosen.index',
            'admin.tugas.index',
            'admin.pengguna.index',
        ];

        foreach ($rute as $nama) {
            $this->actingAs($pengguna)
                ->get(route($nama))
                ->assertForbidden()
                ->assertSee('Anda tidak punya hak akses');
        }
    }

    public function test_admin_dan_staf_bisa_membuka_halaman_panel(): void
    {
        foreach (['admin', 'staf'] as $peran) {
            $pengguna = $this->buatPengguna($peran);

            foreach (['admin.dashboard', 'admin.mahasiswa.index', 'admin.dosen.index', 'admin.tugas.index'] as $nama) {
                $this->actingAs($pengguna)->get(route($nama))->assertOk();
            }
        }
    }

    public function test_staf_tidak_bisa_menghapus_data(): void
    {
        $staf = $this->buatPengguna('staf');
        $mahasiswa = Mahasiswa::factory()->create();
        $dosen = Dosen::factory()->create();
        $tugas = Tugas::factory()->create();

        $this->actingAs($staf)->delete(route('admin.mahasiswa.destroy', $mahasiswa))->assertForbidden();
        $this->actingAs($staf)->delete(route('admin.dosen.destroy', $dosen))->assertForbidden();
        $this->actingAs($staf)->delete(route('admin.tugas.destroy', $tugas))->assertForbidden();

        $this->assertModelExists($mahasiswa);
        $this->assertModelExists($dosen);
        $this->assertModelExists($tugas);
    }

    public function test_admin_bisa_menghapus_data(): void
    {
        $admin = $this->buatPengguna('admin');
        $mahasiswa = Mahasiswa::factory()->create();
        $dosen = Dosen::factory()->create();
        $tugas = Tugas::factory()->create();

        $this->actingAs($admin)->delete(route('admin.mahasiswa.destroy', $mahasiswa))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.dosen.destroy', $dosen))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.tugas.destroy', $tugas))->assertRedirect();

        $this->assertModelMissing($mahasiswa);
        $this->assertModelMissing($dosen);
        $this->assertModelMissing($tugas);
    }

    public function test_staf_bisa_menambah_dan_mengubah_data(): void
    {
        $staf = $this->buatPengguna('staf');
        $departemen = Departemen::factory()->create();

        $this->actingAs($staf)->post(route('admin.tugas.store'), [
            'judul' => 'Tugas Mandiri PBKK 10',
            'deskripsi' => 'Dashboard dan RBAC',
            'departemen_id' => $departemen->id,
            'tenggat' => now()->addWeek()->toDateString(),
        ])->assertRedirect(route('admin.tugas.index'));

        $this->assertDatabaseHas('tugas', ['judul' => 'Tugas Mandiri PBKK 10']);

        $tugas = Tugas::firstOrFail();

        $this->actingAs($staf)->put(route('admin.tugas.update', $tugas), [
            'judul' => 'Judul Baru',
            'departemen_id' => $departemen->id,
            'tenggat' => now()->addWeeks(2)->toDateString(),
        ])->assertRedirect(route('admin.tugas.index'));

        $this->assertDatabaseHas('tugas', ['id' => $tugas->id, 'judul' => 'Judul Baru']);
    }

    public function test_permission_yang_dicabut_langsung_berlaku(): void
    {
        $staf = $this->buatPengguna('staf');

        $this->actingAs($staf)->get(route('admin.tugas.create'))->assertOk();

        // Cabut permission dari peran Staf: controller langsung menolak
        Role::findByName('staf')->revokePermissionTo('tambah-tugas');

        $this->actingAs($staf->fresh())->get(route('admin.tugas.create'))->assertForbidden();
    }

    public function test_hanya_admin_yang_bisa_mengelola_pengguna(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get(route('admin.pengguna.index'))
            ->assertForbidden();

        $this->actingAs($this->buatPengguna('admin'))
            ->get(route('admin.pengguna.index'))
            ->assertOk();
    }

    public function test_admin_bisa_menetapkan_peran_pengguna_lain(): void
    {
        $admin = $this->buatPengguna('admin');
        $target = $this->buatPengguna();

        $this->actingAs($admin)
            ->patch(route('admin.pengguna.update', $target), ['peran' => 'staf'])
            ->assertSessionHas('success');

        $this->assertTrue($target->fresh()->hasRole('staf'));

        $this->actingAs($admin)
            ->patch(route('admin.pengguna.update', $target), ['peran' => ''])
            ->assertSessionHas('success');

        $this->assertFalse($target->fresh()->hasAnyRole(['admin', 'staf']));
    }

    public function test_peran_tidak_dikenal_ditolak(): void
    {
        $admin = $this->buatPengguna('admin');
        $target = $this->buatPengguna();

        $this->actingAs($admin)
            ->patch(route('admin.pengguna.update', $target), ['peran' => 'superuser'])
            ->assertSessionHasErrors('peran');
    }

    public function test_staf_tidak_bisa_mengubah_peran_siapa_pun(): void
    {
        $staf = $this->buatPengguna('staf');
        $target = $this->buatPengguna();

        $this->actingAs($staf)
            ->patch(route('admin.pengguna.update', $target), ['peran' => 'admin'])
            ->assertForbidden();

        $this->assertFalse($target->fresh()->hasRole('admin'));
    }

    public function test_admin_tidak_bisa_mengubah_peran_sendiri(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->actingAs($admin)
            ->patch(route('admin.pengguna.update', $admin), ['peran' => 'staf'])
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_tombol_hapus_hanya_dirender_untuk_admin(): void
    {
        $mahasiswa = Mahasiswa::factory()->create();
        $urlUbah = route('admin.mahasiswa.edit', $mahasiswa);

        // @method('DELETE') hanya muncul di form hapus, jadi cocok sebagai penanda.
        // (URL hapus sama dengan URL ubah tanpa "/edit", jadi tidak cocok dipakai.)
        $penandaHapus = 'name="_method" value="DELETE"';

        $this->actingAs($this->buatPengguna('staf'))
            ->get(route('admin.mahasiswa.index'))
            ->assertOk()
            ->assertSee($urlUbah)
            ->assertDontSee($penandaHapus, false);

        $this->actingAs($this->buatPengguna('admin'))
            ->get(route('admin.mahasiswa.index'))
            ->assertOk()
            ->assertSee($urlUbah)
            ->assertSee($penandaHapus, false);
    }

    // Menu AdminLTE dibangun sekali per instance aplikasi, jadi tiap peran
    // diuji di test terpisah supaya menunya dibangun ulang untuk pengguna itu.
    public function test_menu_sidebar_staf_tidak_memuat_kelola_pengguna(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.mahasiswa.index'))
            ->assertDontSee('Kelola Pengguna')
            ->assertDontSee(route('admin.pengguna.index'));
    }

    public function test_menu_sidebar_admin_memuat_kelola_pengguna(): void
    {
        $this->actingAs($this->buatPengguna('admin'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kelola Pengguna')
            ->assertSee(route('admin.pengguna.index'));
    }
}
