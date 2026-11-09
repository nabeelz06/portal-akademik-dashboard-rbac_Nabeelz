@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="m-0">Dashboard</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="callout callout-info">
        <h5 class="mb-1">Halo, {{ auth()->user()->name }}!</h5>
        <p class="mb-0">
            Anda masuk sebagai <strong>{{ auth()->user()->labelPeran() }}</strong>.
            Menu dan tombol di aplikasi ini menyesuaikan hak akses peran Anda.
        </p>
    </div>

    {{-- Widget statistik: semua angka dihitung dari database lewat model count() --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($jumlahMahasiswa, 0, ',', '.') }}</h3>
                    <p>Total Mahasiswa</p>
                </div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                @can('lihat-mahasiswa')
                    <a href="{{ route('admin.mahasiswa.index') }}" class="small-box-footer">
                        Lihat data <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @endcan
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($jumlahMahasiswaAktif, 0, ',', '.') }}</h3>
                    <p>
                        Mahasiswa Aktif
                        ({{ $jumlahMahasiswa > 0 ? round($jumlahMahasiswaAktif / $jumlahMahasiswa * 100) : 0 }}%)
                    </p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
                @can('lihat-mahasiswa')
                    <a href="{{ route('admin.mahasiswa.index', ['is_active' => 1]) }}" class="small-box-footer">
                        Lihat yang aktif <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @endcan
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($jumlahDosen, 0, ',', '.') }}</h3>
                    <p>Total Dosen</p>
                </div>
                <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                @can('lihat-dosen')
                    <a href="{{ route('admin.dosen.index') }}" class="small-box-footer">
                        Lihat data <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @endcan
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($jumlahTugasAktif, 0, ',', '.') }}</h3>
                    <p>Tugas Aktif (belum lewat tenggat)</p>
                </div>
                <div class="icon"><i class="fas fa-tasks"></i></div>
                @can('lihat-tugas')
                    <a href="{{ route('admin.tugas.index', ['status' => 'aktif']) }}" class="small-box-footer">
                        Lihat tugas aktif <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Departemen</span>
                    <span class="info-box-number">{{ $jumlahDepartemen }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-rata IPK</span>
                    <span class="info-box-number">{{ number_format($rataIpk, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Widget ini hanya muncul untuk Admin (permission kelola-pengguna) --}}
        @can('kelola-pengguna')
            <div class="col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-users-cog"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pengguna Terdaftar</span>
                        <span class="info-box-number">{{ $jumlahPengguna }}</span>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Mahasiswa per Departemen</h3>
                </div>
                <div class="card-body">
                    @php
                        $terbanyak = max($perDepartemen->max('mahasiswa_count'), 1);
                    @endphp

                    @forelse ($perDepartemen as $departemen)
                        <div class="progress-group">
                            <span class="progress-text">{{ $departemen->nama }}</span>
                            <span class="float-right"><b>{{ $departemen->mahasiswa_count }}</b> mahasiswa</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ round($departemen->mahasiswa_count / $terbanyak * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data departemen.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Tugas dengan Tenggat Terdekat</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Tugas</th>
                                <th>Departemen</th>
                                <th class="text-right">Tenggat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tugasTerdekat as $tugas)
                                @php
                                    $sisa = (int) today()->diffInDays($tugas->tenggat);
                                    $warna = $sisa <= 3 ? 'danger' : ($sisa <= 7 ? 'warning' : 'info');
                                @endphp
                                <tr>
                                    <td>{{ \Illuminate\Support\Str::limit($tugas->judul, 38) }}</td>
                                    <td>{{ $tugas->departemen->kode }}</td>
                                    <td class="text-right">
                                        {{ $tugas->tenggat->translatedFormat('d M Y') }}
                                        <span class="badge badge-{{ $warna }}">
                                            {{ $sisa === 0 ? 'Hari ini' : $sisa.' hari lagi' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Tidak ada tugas aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Mahasiswa Terbaru</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>NRP</th>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mahasiswaTerbaru as $mahasiswa)
                                <tr>
                                    <td><code>{{ $mahasiswa->nrp }}</code></td>
                                    <td>{{ $mahasiswa->nama }}</td>
                                    <td>{{ $mahasiswa->departemen->nama }}</td>
                                    <td>
                                        <span class="badge badge-{{ $mahasiswa->is_active ? 'success' : 'secondary' }}">
                                            {{ $mahasiswa->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada mahasiswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Hak Akses Anda</h3>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ([
                        ['Melihat dan mengubah data akademik', auth()->user()->can('ubah-mahasiswa')],
                        ['Menghapus data mahasiswa, dosen, dan tugas', auth()->user()->can('hapus-tugas')],
                        ['Mengelola peran pengguna', auth()->user()->can('kelola-pengguna')],
                    ] as [$teks, $boleh])
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $teks }}
                            <i class="fas {{ $boleh ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@stop
