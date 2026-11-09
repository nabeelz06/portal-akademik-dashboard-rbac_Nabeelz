<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\TugasController;
use App\Http\Controllers\DemoLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman awal: tamu ke login, pengguna yang sudah login ke halaman sesuai perannya
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->rutePulang())
        : redirect()->route('login');
});

// Pengguna biasa (tanpa peran) mendarat di sini setelah login
Route::get('/beranda', HomeController::class)
    ->middleware('auth')
    ->name('home');

// Rute "dashboard" bawaan Breeze dipertahankan sebagai pintu masuk yang
// meneruskan ke halaman sesuai peran, supaya alur Breeze lain tetap jalan.
Route::get('/dashboard', fn () => redirect()->route(auth()->user()->rutePulang()))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel Admin (hanya Admin dan Staf)
|--------------------------------------------------------------------------
| Lapis 1: group ini membatasi peran. Lapis 2: tiap controller memasang
| middleware permission di constructor. Lapis 3: Blade memakai @can.
*/
Route::middleware(['auth', 'role:admin|staf'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // parameters() menjaga nama parameter tetap sama dengan nama resource,
        // karena Laravel otomatis menyingkat "tugas" menjadi "tuga".
        Route::resource('mahasiswa', MahasiswaController::class)
            ->except('show')
            ->parameters(['mahasiswa' => 'mahasiswa']);
        Route::resource('dosen', DosenController::class)
            ->except('show')
            ->parameters(['dosen' => 'dosen']);
        Route::resource('tugas', TugasController::class)
            ->except('show')
            ->parameters(['tugas' => 'tugas']);

        Route::get('pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::patch('pengguna/{user}', [PenggunaController::class, 'update'])->name('pengguna.update');
    });

// Login instan untuk demo (hanya aktif jika DEMO_LOGIN=true dan bukan production)
Route::post('masuk-demo/{akun}', DemoLoginController::class)
    ->middleware('guest')
    ->name('demo.login');

require __DIR__.'/auth.php';
