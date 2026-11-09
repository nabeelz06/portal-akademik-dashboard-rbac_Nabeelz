@extends('adminlte::page')

@section('title', 'Beranda')

@section('content_header')
    <h1 class="m-0">Beranda</h1>
@stop

@section('content')
    @include('partials.flash')

    @php
        $user = auth()->user();
    @endphp

    <div class="card card-outline card-primary">
        <div class="card-body">
            <h4>Selamat datang, {{ $user->name }}</h4>
            <p>
                Peran akun Anda:
                <span class="badge badge-{{ $user->bolehMasukPanel() ? 'primary' : 'secondary' }}">{{ $user->labelPeran() }}</span>
            </p>

            @if ($user->bolehMasukPanel())
                <p>Akun Anda punya akses ke panel admin.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt mr-1"></i> Buka Dashboard
                </a>
            @else
                <p>
                    Akun Anda belum punya akses ke panel admin. Minta Admin menetapkan peran
                    <strong>Staf</strong> atau <strong>Admin</strong> lewat menu Kelola Pengguna.
                </p>
            @endif

            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                <i class="fas fa-user-cog mr-1"></i> Profil Saya
            </a>
        </div>
    </div>
@stop
