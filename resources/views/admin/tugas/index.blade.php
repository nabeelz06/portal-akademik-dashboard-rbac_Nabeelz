@extends('adminlte::page')

@section('title', 'Tugas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Data Tugas</h1>

        @can('tambah-tugas')
            <a href="{{ route('admin.tugas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Tugas
            </a>
        @endcan
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('admin.tugas.index') }}" class="form-row align-items-end">
                <div class="col-lg-4 col-md-6">
                    <x-field name="q" type="search" label="Cari judul tugas" :value="request('q')"
                        placeholder="Contoh: Basis Data" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="departemen_id" label="Departemen" :options="$departemen"
                        :value="request('departemen_id')" placeholder="Semua departemen" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="status" label="Status tenggat"
                        :options="['aktif' => 'Masih aktif', 'lewat' => 'Sudah lewat tenggat']"
                        :value="request('status')" placeholder="Semua" />
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.tugas.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Departemen</th>
                        <th>Pemberi Tugas</th>
                        <th>Tenggat</th>
                        <th class="aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugas as $item)
                        @php
                            $aktif = $item->tenggat->greaterThanOrEqualTo(today());
                        @endphp
                        <tr>
                            <td>{{ $tugas->firstItem() + $loop->index }}</td>
                            <td>
                                {{ $item->judul }}
                                @if ($item->deskripsi)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($item->deskripsi, 70) }}</small>
                                @endif
                            </td>
                            <td>{{ $item->departemen->nama }}</td>
                            <td>{{ $item->dosen?->nama ?? '-' }}</td>
                            <td>
                                {{ $item->tenggat->translatedFormat('d M Y') }}
                                <span class="badge badge-{{ $aktif ? 'success' : 'secondary' }}">
                                    {{ $aktif ? 'Aktif' : 'Lewat' }}
                                </span>
                            </td>
                            <td class="aksi text-right">
                                @can('ubah-tugas')
                                    <a href="{{ route('admin.tugas.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Ubah
                                    </a>
                                @endcan

                                {{-- Tombol Hapus hanya dirender untuk permission hapus-tugas (Admin) --}}
                                @can('hapus-tugas')
                                    <form action="{{ route('admin.tugas.destroy', $item) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm(@js('Hapus tugas "'.$item->judul.'"? Tindakan ini tidak dapat dibatalkan.'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada tugas yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginasi', ['data' => $tugas])
    </div>
@stop
