@extends('adminlte::page')

@section('title', $tugas->exists ? 'Ubah Tugas' : 'Tambah Tugas')

@section('content_header')
    <h1 class="m-0">{{ $tugas->exists ? 'Ubah Tugas' : 'Tambah Tugas' }}</h1>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <form method="POST"
            action="{{ $tugas->exists ? route('admin.tugas.update', $tugas) : route('admin.tugas.store') }}">
            @csrf
            @if ($tugas->exists)
                @method('PUT')
            @endif

            <div class="card-body">
                <x-field name="judul" label="Judul tugas" :value="$tugas->judul" maxlength="150" required />

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" maxlength="2000"
                        class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-select name="departemen_id" label="Departemen" :options="$departemen"
                            :value="$tugas->departemen_id" placeholder="Pilih departemen" required />
                    </div>
                    <div class="col-md-5">
                        <x-select name="dosen_id" label="Pemberi tugas (dosen)" :options="$dosen"
                            :value="$tugas->dosen_id" placeholder="Belum ditentukan" />
                    </div>
                    <div class="col-md-3">
                        <x-field name="tenggat" type="date" label="Tenggat"
                            :value="$tugas->tenggat?->format('Y-m-d')" required />
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                <a href="{{ route('admin.tugas.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
