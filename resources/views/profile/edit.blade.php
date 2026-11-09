@extends('adminlte::page')

@section('title', 'Profil Saya')

@section('content_header')
    <h1 class="m-0">Profil Saya</h1>
@stop

@section('content')
    @include('partials.flash')

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Informasi Akun</h3>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">
                        <x-field name="name" label="Nama" :value="$user->name" autocomplete="name" required />
                        <x-field name="email" type="email" label="Email" :value="$user->email"
                            autocomplete="username" required />

                        <p class="mb-0 text-muted">
                            Peran akun:
                            <span class="badge badge-info">{{ $user->labelPeran() }}</span>
                        </p>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Ubah Kata Sandi</h3>
                </div>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <x-field name="current_password" id="ubah_sandi_lama" type="password" bag="updatePassword"
                            label="Kata sandi saat ini" autocomplete="current-password" required />
                        <x-field name="password" id="ubah_sandi_baru" type="password" bag="updatePassword"
                            label="Kata sandi baru" autocomplete="new-password" required />
                        <x-field name="password_confirmation" id="ubah_sandi_ulang" type="password" bag="updatePassword"
                            label="Ulangi kata sandi baru" autocomplete="new-password" required />
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Perbarui Kata Sandi</button>
                    </div>
                </form>
            </div>

            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Hapus Akun</h3>
                </div>

                <form action="{{ route('profile.destroy') }}" method="POST"
                    onsubmit="return confirm('Hapus akun secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')

                    <div class="card-body">
                        <p class="text-muted">
                            Setelah dihapus, akun dan seluruh datanya hilang permanen. Masukkan kata sandi
                            untuk memastikan.
                        </p>
                        <x-field name="password" id="hapus_sandi" type="password" bag="userDeletion"
                            label="Kata sandi" autocomplete="current-password" required />
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
