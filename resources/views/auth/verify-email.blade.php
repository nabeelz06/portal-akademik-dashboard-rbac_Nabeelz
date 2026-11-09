@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Verifikasi Email')

@section('auth_header', 'Verifikasi email Anda')

@section('auth_body')
    <p class="text-muted">
        Terima kasih sudah mendaftar. Klik tautan verifikasi yang kami kirim ke email Anda.
        Jika belum menerimanya, kami bisa mengirim ulang.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">Tautan verifikasi baru sudah dikirim ke email Anda.</div>
    @endif

    <form action="{{ route('verification.send') }}" method="POST" class="mb-2">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">Kirim ulang email verifikasi</button>
    </form>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-link btn-block">Keluar</button>
    </form>
@stop
