<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    /**
     * Peran yang bisa ditetapkan lewat halaman ini.
     */
    private const PERAN = ['admin', 'staf'];

    public function __construct()
    {
        // Seluruh halaman ini khusus Admin (permission kelola-pengguna)
        $this->middleware('permission:kelola-pengguna');
    }

    public function index(Request $request): View
    {
        $cari = $this->kataKunci($request);
        $peran = $request->query('peran');

        $pengguna = User::query()
            ->with('roles')
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($sub) use ($cari) {
                    $sub->where('name', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });
            })
            ->when(in_array($peran, self::PERAN, true), fn ($query) => $query->role($peran))
            ->when($peran === 'tanpa', fn ($query) => $query->doesntHave('roles'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengguna.index', [
            'pengguna' => $pengguna,
            'daftarPeran' => self::PERAN,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'peran' => ['nullable', Rule::in(self::PERAN)],
        ]);

        // Mencegah Admin mengunci dirinya sendiri dari panel
        if ($user->is($request->user())) {
            return back()->with('error', 'Peran akun Anda sendiri tidak dapat diubah.');
        }

        $user->syncRoles($data['peran'] ? [$data['peran']] : []);

        return back()->with(
            'success',
            "Peran {$user->name} sekarang: {$user->fresh()->labelPeran()}."
        );
    }
}
