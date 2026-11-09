<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji ke mana pengguna diarahkan setelah login, sesuai perannya.
 */
class AlurPeranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siapkanPeran();
    }

    public function test_admin_masuk_ke_dashboard_admin(): void
    {
        $admin = $this->buatPengguna('admin');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_staf_masuk_ke_dashboard_admin(): void
    {
        $staf = $this->buatPengguna('staf');

        $this->post('/login', ['email' => $staf->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_pengguna_biasa_masuk_ke_beranda(): void
    {
        $pengguna = $this->buatPengguna();

        $this->post('/login', ['email' => $pengguna->email, 'password' => 'password'])
            ->assertRedirect(route('home', absolute: false));
    }

    public function test_pengguna_biasa_tidak_diarahkan_ke_halaman_admin_yang_diminta(): void
    {
        $pengguna = $this->buatPengguna();

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post('/login', ['email' => $pengguna->email, 'password' => 'password'])
            ->assertRedirect(route('home', absolute: false));
    }

    public function test_pengguna_yang_sudah_login_dialihkan_dari_halaman_login(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get('/login')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($this->buatPengguna())
            ->get('/login')
            ->assertRedirect(route('home'));
    }

    public function test_halaman_awal_mengarahkan_sesuai_status_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));

        $this->actingAs($this->buatPengguna('admin'))->get('/')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->buatPengguna())->get('/')->assertRedirect(route('home'));
    }

    public function test_rute_dashboard_bawaan_breeze_meneruskan_sesuai_peran(): void
    {
        $this->actingAs($this->buatPengguna('staf'))
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($this->buatPengguna())
            ->get('/dashboard')
            ->assertRedirect(route('home'));
    }

    public function test_akun_baru_dari_registrasi_tidak_punya_peran(): void
    {
        $this->post('/register', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('home', absolute: false));

        $this->assertFalse(auth()->user()->bolehMasukPanel());

        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_beranda_pengguna_biasa_menjelaskan_keterbatasan_akses(): void
    {
        $this->actingAs($this->buatPengguna())
            ->get(route('home'))
            ->assertOk()
            ->assertSee('belum punya akses ke panel admin');
    }
}
