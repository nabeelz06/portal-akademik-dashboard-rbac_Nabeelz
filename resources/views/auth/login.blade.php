@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Masuk')

@section('auth_header', 'Masuk ke Portal Akademik')

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <x-field name="email" type="email" icon="fa-envelope" placeholder="Email"
            autocomplete="username" autofocus required />
        <x-field name="password" type="password" icon="fa-lock" placeholder="Kata sandi"
            autocomplete="current-password" required />

        <div class="row align-items-center">
            <div class="col-7">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remember" name="remember"
                        @checked(old('remember'))>
                    <label class="custom-control-label" for="remember">Ingat saya</label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                </button>
            </div>
        </div>
    </form>

    {{-- Login instan untuk demo (aktif jika DEMO_LOGIN=true dan bukan production) --}}
    @if (config('demo.enabled'))
        <div class="demo-login mt-4">
            <p class="text-center text-muted mb-2">
                <small>atau masuk cepat sebagai akun demo</small>
            </p>

            @foreach (config('demo.accounts') as $kunci => $akun)
                <form action="{{ route('demo.login', $kunci) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $akun['button'] }} d-flex align-items-center">
                        <i class="fas {{ $akun['icon'] }} fa-fw mr-3"></i>
                        <span class="flex-grow-1">
                            <strong>{{ $akun['label'] }}</strong><br>
                            <small>{{ $akun['hint'] }}</small>
                        </span>
                    </button>
                </form>
            @endforeach
        </div>
    @endif
@stop

@section('auth_footer')
    <p class="my-0"><a href="{{ route('password.request') }}">Lupa kata sandi?</a></p>
    <p class="my-0"><a href="{{ route('register') }}">Daftar akun baru</a></p>
@stop
