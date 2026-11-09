@extends('adminlte::page')

@section('title', 'Kelola Pengguna')

@section('content_header')
    <h1 class="m-0">Kelola Pengguna</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="callout callout-warning">
        <p class="mb-0">
            Halaman ini khusus <strong>Admin</strong>. Pengguna yang mendaftar sendiri belum punya peran
            sampai Anda menetapkannya di sini. Peran akun Anda sendiri tidak bisa diubah.
        </p>
    </div>

    <div class="card">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="form-row align-items-end">
                <div class="col-lg-5 col-md-6">
                    <x-field name="q" type="search" label="Cari nama atau email" :value="request('q')"
                        placeholder="Contoh: staf@akademik.test" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-select name="peran" label="Peran"
                        :options="['admin' => 'Admin', 'staf' => 'Staf', 'tanpa' => 'Pengguna Biasa']"
                        :value="request('peran')" placeholder="Semua peran" />
                </div>
                <div class="col-lg-4 col-md-12 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Terdaftar</th>
                        <th class="text-right">Ubah peran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengguna as $item)
                        @php
                            $diriSendiri = $item->is(auth()->user());
                            $warna = match (true) {
                                $item->hasRole('admin') => 'primary',
                                $item->hasRole('staf') => 'info',
                                default => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $pengguna->firstItem() + $loop->index }}</td>
                            <td>
                                {{ $item->name }}
                                @if ($diriSendiri)
                                    <span class="badge badge-light border ml-1">Anda</span>
                                @endif
                            </td>
                            <td>{{ $item->email }}</td>
                            <td><span class="badge badge-{{ $warna }}">{{ $item->labelPeran() }}</span></td>
                            <td>{{ $item->created_at->translatedFormat('d M Y') }}</td>
                            <td class="aksi">
                                <form method="POST" action="{{ route('admin.pengguna.update', $item) }}"
                                    class="form-inline justify-content-end">
                                    @csrf
                                    @method('PATCH')
                                    <select name="peran" class="form-control form-control-sm mr-2"
                                        aria-label="Peran untuk {{ $item->name }}" @disabled($diriSendiri)>
                                        <option value="">Pengguna Biasa</option>
                                        @foreach ($daftarPeran as $peran)
                                            <option value="{{ $peran }}" @selected($item->hasRole($peran))>{{ ucfirst($peran) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary" @disabled($diriSendiri)>Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada pengguna yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginasi', ['data' => $pengguna])
    </div>
@stop
