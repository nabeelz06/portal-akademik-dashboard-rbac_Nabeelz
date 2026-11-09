@extends('adminlte::page')

@section('title', 'Akses Ditolak')

@section('content_header')
    <h1 class="m-0">Akses Ditolak</h1>
@stop

@section('content')
    <div class="error-page">
        <h2 class="headline text-danger">403</h2>

        <div class="error-content pt-4">
            <h3><i class="fas fa-lock text-danger"></i> Anda tidak punya hak akses.</h3>

            <p>
                Peran akun Anda ({{ auth()->user()?->labelPeran() }}) tidak diizinkan membuka halaman
                atau menjalankan aksi ini. Hubungi Admin jika menurut Anda ini keliru.
            </p>

            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
@stop
