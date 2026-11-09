@extends('adminlte::page')

@section('title', 'Mahasiswa')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Data Mahasiswa</h1>

        @can('tambah-mahasiswa')
            <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Mahasiswa
            </a>
        @endcan
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="form-row align-items-end">
                <div class="col-lg-4 col-md-6">
                    <x-field name="q" type="search" label="Cari nama atau NRP" :value="request('q')"
                        placeholder="Contoh: Nabil atau 5025241024" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="departemen_id" label="Departemen" :options="$departemen"
                        :value="request('departemen_id')" placeholder="Semua departemen" />
                </div>
                <div class="col-lg-2 col-md-4">
                    <x-select name="angkatan" label="Angkatan" :options="$daftarAngkatan"
                        :value="request('angkatan')" placeholder="Semua" />
                </div>
                <div class="col-lg-3 col-md-4">
                    <x-select name="is_active" label="Status" :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                        :value="request('is_active')" placeholder="Semua" />
                </div>
                <div class="col-12 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NRP</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Angkatan</th>
                        <th>IPK</th>
                        <th>Dosen Wali</th>
                        <th>Status</th>
                        <th class="aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswa as $item)
                        <tr>
                            <td>{{ $mahasiswa->firstItem() + $loop->index }}</td>
                            <td><code>{{ $item->nrp }}</code></td>
                            <td>
                                {{ $item->nama }}<br>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>
                            <td>{{ $item->departemen->nama }}</td>
                            <td>{{ $item->angkatan }}</td>
                            <td>{{ number_format($item->ipk, 2, ',', '.') }}</td>
                            <td>{{ $item->dosenWali?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="aksi text-right">
                                @can('ubah-mahasiswa')
                                    <a href="{{ route('admin.mahasiswa.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Ubah
                                    </a>
                                @endcan

                                {{-- Tombol Hapus hanya dirender untuk yang berhak (Admin) --}}
                                @can('hapus-mahasiswa')
                                    <form action="{{ route('admin.mahasiswa.destroy', $item) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm(@js('Hapus data '.$item->nama.'? Tindakan ini tidak dapat dibatalkan.'))">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada mahasiswa yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginasi', ['data' => $mahasiswa])
    </div>
@stop
