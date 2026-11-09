@extends('adminlte::page')

@section('title', $dosen->exists ? 'Ubah Dosen' : 'Tambah Dosen')

@section('content_header')
    <h1 class="m-0">{{ $dosen->exists ? 'Ubah Data Dosen' : 'Tambah Dosen' }}</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <form method="POST"
            action="{{ $dosen->exists ? route('admin.dosen.update', $dosen) : route('admin.dosen.store') }}">
            @csrf
            @if ($dosen->exists)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-field name="nip" label="NIP" :value="$dosen->nip" inputmode="numeric" maxlength="18"
                            hint="18 digit angka" required />
                    </div>
                    <div class="col-md-6">
                        <x-field name="nama" label="Nama lengkap (beserta gelar)" :value="$dosen->nama"
                            maxlength="150" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-field name="email" type="email" label="Email" :value="$dosen->email" maxlength="100" required />
                    </div>
                    <div class="col-md-3">
                        <x-select name="departemen_id" label="Departemen" :options="$departemen"
                            :value="$dosen->departemen_id" placeholder="Pilih departemen" required />
                    </div>
                    <div class="col-md-3">
                        <x-select name="jabatan" label="Jabatan" :options="$daftarJabatan"
                            :value="$dosen->jabatan" placeholder="Belum ditentukan" />
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                <a href="{{ route('admin.dosen.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
