@extends('adminlte::page')

@section('title', 'Dosen')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Data Dosen</h1>

        @can('tambah-dosen')
            <a href="{{ route('admin.dosen.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Dosen
            </a>
        @endcan
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="card">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('admin.dosen.index') }}" class="form-row align-items-end">
                <div class="col-lg-4 col-md-6">
                    <x-field name="q" type="search" label="Cari nama, NIP, atau email" :value="request('q')"
                        placeholder="Contoh: Lektor atau 1980..." />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="departemen_id" label="Departemen" :options="$departemen"
                        :value="request('departemen_id')" placeholder="Semua departemen" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="jabatan" label="Jabatan" :options="$daftarJabatan"
                        :value="request('jabatan')" placeholder="Semua jabatan" />
                </div>
                <div class="col-lg-2 col-md-6 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Mahasiswa Wali</th>
                        <th class="aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dosen as $item)
                        <tr>
                            <td>{{ $dosen->firstItem() + $loop->index }}</td>
                            <td><code>{{ $item->nip }}</code></td>
                            <td>
                                {{ $item->nama }}<br>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>
                            <td>{{ $item->departemen->nama }}</td>
                            <td>{{ $item->jabatan ?? '-' }}</td>
                            <td>{{ $item->mahasiswa_wali_count }}</td>
                            <td class="aksi text-right">
                                @can('ubah-dosen')
                                    <a href="{{ route('admin.dosen.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Ubah
                                    </a>
                                @endcan

                                @can('hapus-dosen')
                                    <form action="{{ route('admin.dosen.destroy', $item) }}" method="POST" class="d-inline"
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
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada dosen yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginasi', ['data' => $dosen])
    </div>
@stop
