@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', 'Atur Ulang Kata Sandi')

@section('auth_header', 'Atur ulang kata sandi')

@section('auth_body')
    <form action="{{ route('password.store') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-field name="email" type="email" icon="fa-envelope" :value="$request->email" placeholder="Email"
            autocomplete="username" required />
        <x-field name="password" type="password" icon="fa-lock" placeholder="Kata sandi baru"
            autocomplete="new-password" autofocus required />
        <x-field name="password_confirmation" type="password" icon="fa-lock" placeholder="Ulangi kata sandi baru"
            autocomplete="new-password" required />

        <button type="submit" class="btn btn-primary btn-block">Atur ulang kata sandi</button>
    </form>
@stop
