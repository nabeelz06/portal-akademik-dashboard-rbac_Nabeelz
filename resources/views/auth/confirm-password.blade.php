@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Konfirmasi Kata Sandi')

@section('auth_header', 'Konfirmasi kata sandi')

@section('auth_body')
    <p class="text-muted">
        Ini area aman. Masukkan kata sandi Anda untuk melanjutkan.
    </p>

    <form action="{{ route('password.confirm') }}" method="POST">
        @csrf

        <x-field name="password" type="password" icon="fa-lock" placeholder="Kata sandi"
            autocomplete="current-password" autofocus required />

        <button type="submit" class="btn btn-primary btn-block">Konfirmasi</button>
    </form>
@stop
