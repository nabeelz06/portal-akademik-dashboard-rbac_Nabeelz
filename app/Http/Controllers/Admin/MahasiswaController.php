<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lihat-mahasiswa')->only('index');
        $this->middleware('permission:tambah-mahasiswa')->only(['create', 'store']);
        $this->middleware('permission:ubah-mahasiswa')->only(['edit', 'update']);
        $this->middleware('permission:hapus-mahasiswa')->only('destroy');
    }

    public function index(Request $request): View
    {
        $cari = $this->kataKunci($request);

        $mahasiswa = Mahasiswa::query()
            ->with(['departemen', 'dosenWali'])
            // Cari nama ATAU NRP. Sub-closure menjaga OR tidak merusak filter lain.
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama', 'like', "%{$cari}%")
                        ->orWhere('nrp', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('departemen_id'), function ($query) use ($request) {
                $query->where('departemen_id', $request->integer('departemen_id'));
            })
            ->when($request->filled('angkatan'), function ($query) use ($request) {
                $query->where('angkatan', $request->integer('angkatan'));
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString(); // filter tetap terbawa saat pindah halaman

        return view('admin.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'departemen' => Departemen::orderBy('nama')->pluck('nama', 'id'),
            'daftarAngkatan' => Mahasiswa::query()->distinct()->orderByDesc('angkatan')->pluck('angkatan', 'angkatan'),
        ]);
    }

    public function create(): View
    {
        return view('admin.mahasiswa.form', [
            'mahasiswa' => new Mahasiswa(['is_active' => true, 'angkatan' => now()->year]),
            ...$this->opsiForm(),
        ]);
    }

    public function store(MahasiswaRequest $request): RedirectResponse
    {
        Mahasiswa::create($request->validated());

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('admin.mahasiswa.form', [
            'mahasiswa' => $mahasiswa,
            ...$this->opsiForm(),
        ]);
    }

    public function update(MahasiswaRequest $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->update($request->validated());

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    /**
     * Hanya Admin yang punya permission hapus-mahasiswa (lihat constructor).
     */
    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return back()->with('success', "Data mahasiswa {$nama} berhasil dihapus.");
    }

    /**
     * @return array{departemen: \Illuminate\Support\Collection, dosen: \Illuminate\Support\Collection}
     */
    private function opsiForm(): array
    {
        return [
            'departemen' => Departemen::orderBy('nama')->pluck('nama', 'id'),
            'dosen' => Dosen::orderBy('nama')->pluck('nama', 'id'),
        ];
    }
}
