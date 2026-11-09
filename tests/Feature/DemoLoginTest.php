<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    private function aktifkanDemo(): void
    {
        config(['demo.enabled' => true]);

        $this->siapkanPeran();
        $this->seed(UserSeeder::class);
    }

    public function test_tombol_demo_tampil_di_halaman_login_saat_aktif(): void
    {
        config(['demo.enabled' => true]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('demo.login', 'admin'))
            ->assertSee(route('demo.login', 'staf'))
            ->assertSee(route('demo.login', 'pengguna'));
    }

    public function test_tombol_demo_tidak_tampil_saat_dimatikan(): void
    {
        config(['demo.enabled' => false]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee(route('demo.login', 'admin'));
    }

    public function test_login_instan_sebagai_admin(): void
    {
        $this->aktifkanDemo();

        $this->post(route('demo.login', 'admin'))->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('admin'));
    }

    public function test_login_instan_sebagai_staf(): void
    {
        $this->aktifkanDemo();

        $this->post(route('demo.login', 'staf'))->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(auth()->user()->hasRole('staf'));
    }

    public function test_login_instan_sebagai_pengguna_biasa_ke_beranda(): void
    {
        $this->aktifkanDemo();

        $this->post(route('demo.login', 'pengguna'))->assertRedirect(route('home'));

        $this->assertFalse(auth()->user()->bolehMasukPanel());
    }

    public function test_login_instan_mati_saat_dimatikan(): void
    {
        $this->siapkanPeran();
        $this->seed(UserSeeder::class);
        config(['demo.enabled' => false]);

        $this->post(route('demo.login', 'admin'))->assertNotFound();

        $this->assertGuest();
    }

    public function test_akun_tidak_dikenal_ditolak(): void
    {
        $this->aktifkanDemo();

        $this->post(route('demo.login', 'hacker'))->assertNotFound();
        $this->post(route('demo.login', 'admin.email'))->assertNotFound();

        $this->assertGuest();
    }

    public function test_akun_demo_belum_di_seed_kembali_ke_login(): void
    {
        config(['demo.enabled' => true]);

        $this->post(route('demo.login', 'admin'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_akun_demo_tidak_bisa_dihapus_dari_profil(): void
    {
        $this->aktifkanDemo();

        $admin = User::where('email', 'admin@akademik.test')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('profile.destroy'), ['password' => config('demo.password')])
            ->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertModelExists($admin);
    }
}
