@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Lupa Kata Sandi')

@section('auth_header', 'Lupa kata sandi?')

@section('auth_body')
    <p class="text-muted">
        Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang kata sandi.
    </p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <x-field name="email" type="email" icon="fa-envelope" placeholder="Email" autofocus required />

        <button type="submit" class="btn btn-primary btn-block">Kirim tautan atur ulang</button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
@stop
