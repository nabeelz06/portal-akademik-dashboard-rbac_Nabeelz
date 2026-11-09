<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    /**
     * Login instan sebagai salah satu akun uji coba (config/demo.php).
     *
     * Hanya untuk demo. Rute ini mati (404) kecuali DEMO_LOGIN=true,
     * dan selalu mati di lingkungan production.
     */
    public function __invoke(Request $request, string $akun): RedirectResponse
    {
        abort_unless(config('demo.enabled'), 404);

        $daftarAkun = config('demo.accounts');
        abort_unless(array_key_exists($akun, $daftarAkun), 404);

        $user = User::where('email', $daftarAkun[$akun]['email'])->first();

        if ($user === null) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun demo belum ada. Jalankan: php artisan db:seed']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->rutePulang());
    }
}
