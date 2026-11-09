@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('title', 'Daftar')

@section('auth_header', 'Buat akun baru')

@section('auth_body')
    <form action="{{ route('register') }}" method="POST">
        @csrf

        <x-field name="name" icon="fa-user" placeholder="Nama lengkap" autocomplete="name" autofocus required />
        <x-field name="email" type="email" icon="fa-envelope" placeholder="Email" autocomplete="username" required />
        <x-field name="password" type="password" icon="fa-lock" placeholder="Kata sandi (minimal 8 karakter)"
            autocomplete="new-password" required />
        <x-field name="password_confirmation" type="password" icon="fa-lock" placeholder="Ulangi kata sandi"
            autocomplete="new-password" required />

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-user-plus mr-1"></i> Daftar
        </button>

        <p class="text-muted small mt-3 mb-0">
            Akun baru belum punya peran. Admin perlu menetapkan peran agar Anda bisa membuka panel.
        </p>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@stop
