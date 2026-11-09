<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lihat-dashboard');
    }

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            // Widget statistik: semua angka dihitung langsung dari database
            'jumlahMahasiswa' => Mahasiswa::count(),
            'jumlahMahasiswaAktif' => Mahasiswa::where('is_active', true)->count(),
            'jumlahDosen' => Dosen::count(),
            'jumlahTugasAktif' => Tugas::aktif()->count(),
            'jumlahDepartemen' => Departemen::count(),
            'rataIpk' => (float) Mahasiswa::avg('ipk'),
            'jumlahPengguna' => User::count(),

            'perDepartemen' => Departemen::withCount('mahasiswa')
                ->orderByDesc('mahasiswa_count')
                ->get(),
            'mahasiswaTerbaru' => Mahasiswa::with('departemen')
                ->latest('id')
                ->limit(5)
                ->get(),
            'tugasTerdekat' => Tugas::aktif()
                ->with('departemen')
                ->orderBy('tenggat')
                ->limit(5)
                ->get(),
        ]);
    }
}
