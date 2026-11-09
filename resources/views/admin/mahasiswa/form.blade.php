@extends('adminlte::page')

@section('title', $mahasiswa->exists ? 'Ubah Mahasiswa' : 'Tambah Mahasiswa')

@section('content_header')
    <h1 class="m-0">{{ $mahasiswa->exists ? 'Ubah Data Mahasiswa' : 'Tambah Mahasiswa' }}</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <form method="POST"
            action="{{ $mahasiswa->exists ? route('admin.mahasiswa.update', $mahasiswa) : route('admin.mahasiswa.store') }}">
            @csrf
            @if ($mahasiswa->exists)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-field name="nrp" label="NRP" :value="$mahasiswa->nrp" inputmode="numeric" maxlength="10"
                            hint="10 digit angka" required />
                    </div>
                    <div class="col-md-6">
                        <x-field name="nama" label="Nama lengkap" :value="$mahasiswa->nama" maxlength="100" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-field name="email" type="email" label="Email" :value="$mahasiswa->email" maxlength="100" required />
                    </div>
                    <div class="col-md-6">
                        <x-select name="departemen_id" label="Departemen" :options="$departemen"
                            :value="$mahasiswa->departemen_id" placeholder="Pilih departemen" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-select name="dosen_wali_id" label="Dosen wali" :options="$dosen"
                            :value="$mahasiswa->dosen_wali_id" placeholder="Belum ditentukan" />
                    </div>
                    <div class="col-md-2">
                        <x-field name="angkatan" type="number" label="Angkatan" :value="$mahasiswa->angkatan"
                            min="2015" max="{{ now()->year }}" required />
                    </div>
                    <div class="col-md-2">
                        <x-field name="ipk" type="number" label="IPK" :value="$mahasiswa->ipk" step="0.01"
                            min="0" max="4" required />
                    </div>
                    <div class="col-md-2">
                        <x-select name="is_active" label="Status" :options="['1' => 'Aktif', '0' => 'Nonaktif']"
                            :value="$mahasiswa->is_active ? '1' : '0'" required />
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
