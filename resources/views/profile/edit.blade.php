@extends('layouts.app')

@section('title', 'Profil — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16 max-w-3xl">
    <h1 class="text-headline-lg mb-2">Profil Saya</h1>
    <p class="text-text-secondary text-sm mb-8">Kelola informasi akun dan password Anda.</p>

    @include('partials.alerts')

    <div class="space-y-8">
        {{-- Profile info --}}
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="text-headline-sm mb-6">Informasi Akun</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="w-full">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group mb-0! sm:col-span-2">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="form-input @error('name') is-error @enderror">
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-0! sm:col-span-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="form-input @error('email') is-error @enderror">
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-8">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Password --}}
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="text-headline-sm mb-2">Ubah Password</h2>
            <p class="text-sm text-text-secondary mb-6">Password harus minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol.</p>
            <form method="POST" action="{{ route('profile.password') }}" class="w-full">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group mb-0! sm:col-span-2">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" required
                            class="form-input @error('current_password') is-error @enderror" placeholder="Password saat ini">
                        @error('current_password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-0!">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" required
                            class="form-input @error('password') is-error @enderror" placeholder="Minimal 8 karakter">
                        @error('password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-0!">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="form-input @error('password') is-error @enderror" placeholder="Ulangi password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-8">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
@endsection