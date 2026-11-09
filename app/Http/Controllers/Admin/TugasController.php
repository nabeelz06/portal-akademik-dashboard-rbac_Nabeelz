<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TugasRequest;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lihat-tugas')->only('index');
        $this->middleware('permission:tambah-tugas')->only(['create', 'store']);
        $this->middleware('permission:ubah-tugas')->only(['edit', 'update']);
        // Pola dari materi: hanya yang punya permission hapus-tugas (Admin)
        // yang bisa menjalankan destroy.
        $this->middleware('permission:hapus-tugas')->only('destroy');
    }

    public function index(Request $request): View
    {
        $cari = $this->kataKunci($request);
        $status = $request->query('status');

        $tugas = Tugas::query()
            ->with(['departemen', 'dosen'])
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where('judul', 'like', "%{$cari}%");
            })
            ->when($request->filled('departemen_id'), function ($query) use ($request) {
                $query->where('departemen_id', $request->integer('departemen_id'));
            })
            ->when($status === 'aktif', fn ($query) => $query->aktif())
            ->when($status === 'lewat', fn ($query) => $query->whereDate('tenggat', '<', today()))
            ->orderBy('tenggat')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tugas.index', [
            'tugas' => $tugas,
            'departemen' => Departemen::orderBy('nama')->pluck('nama', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('admin.tugas.form', [
            'tugas' => new Tugas(['tenggat' => now()->addWeek()]),
            ...$this->opsiForm(),
        ]);
    }

    public function store(TugasRequest $request): RedirectResponse
    {
        Tugas::create($request->validated());

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Tugas $tugas): View
    {
        return view('admin.tugas.form', [
            'tugas' => $tugas,
            ...$this->opsiForm(),
        ]);
    }

    public function update(TugasRequest $request, Tugas $tugas): RedirectResponse
    {
        $tugas->update($request->validated());

        return redirect()
            ->route('admin.tugas.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Tugas $tugas): RedirectResponse
    {
        $judul = $tugas->judul;
        $tugas->delete();

        return back()->with('success', "Tugas \"{$judul}\" berhasil dihapus.");
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
