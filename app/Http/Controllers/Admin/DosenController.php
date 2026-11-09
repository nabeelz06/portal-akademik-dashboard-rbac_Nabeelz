<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DosenRequest;
use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:lihat-dosen')->only('index');
        $this->middleware('permission:tambah-dosen')->only(['create', 'store']);
        $this->middleware('permission:ubah-dosen')->only(['edit', 'update']);
        $this->middleware('permission:hapus-dosen')->only('destroy');
    }

    public function index(Request $request): View
    {
        $cari = $this->kataKunci($request);
        $jabatan = $request->query('jabatan');

        $dosen = Dosen::query()
            ->with('departemen')
            ->withCount('mahasiswaWali')
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama', 'like', "%{$cari}%")
                        ->orWhere('nip', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('departemen_id'), function ($query) use ($request) {
                $query->where('departemen_id', $request->integer('departemen_id'));
            })
            ->when(in_array($jabatan, Dosen::JABATAN, true), function ($query) use ($jabatan) {
                $query->where('jabatan', $jabatan);
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dosen.index', [
            'dosen' => $dosen,
            'departemen' => Departemen::orderBy('nama')->pluck('nama', 'id'),
            'daftarJabatan' => array_combine(Dosen::JABATAN, Dosen::JABATAN),
        ]);
    }

    public function create(): View
    {
        return view('admin.dosen.form', [
            'dosen' => new Dosen(),
            ...$this->opsiForm(),
        ]);
    }

    public function store(DosenRequest $request): RedirectResponse
    {
        Dosen::create($request->validated());

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen): View
    {
        return view('admin.dosen.form', [
            'dosen' => $dosen,
            ...$this->opsiForm(),
        ]);
    }

    public function update(DosenRequest $request, Dosen $dosen): RedirectResponse
    {
        $dosen->update($request->validated());

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hanya Admin yang punya permission hapus-dosen (lihat constructor).
     */
    public function destroy(Dosen $dosen): RedirectResponse
    {
        $nama = $dosen->nama;
        $dosen->delete();

        return back()->with('success', "Data dosen {$nama} berhasil dihapus.");
    }

    /**
     * @return array{departemen: \Illuminate\Support\Collection, daftarJabatan: array<string, string>}
     */
    private function opsiForm(): array
    {
        return [
            'departemen' => Departemen::orderBy('nama')->pluck('nama', 'id'),
            'daftarJabatan' => array_combine(Dosen::JABATAN, Dosen::JABATAN),
        ];
    }
}
