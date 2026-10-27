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
            <div class="flex items-center gap-4 mb-6">
                @if ($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover">
                @else
                    <span class="w-20 h-20 rounded-full bg-obsidian text-white text-2xl font-bold flex items-center justify-center">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
                <div>
                    <p class="text-title-md font-semibold">{{ $user->name }}</p>
                    <p class="text-sm text-text-muted">{{ $user->email }}</p>
                    <span class="badge badge-primary mt-1">{{ $user->roleLabel() }}</span>
                </div>
                <div class="flex-grow"></div>
                <a href="{{ $user->dashboardRoute() }}" class="btn btn-outline btn-sm">Dashboard</a>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="w-full">
                @csrf
                @method('PATCH')
                <div class="form-group mb-0!">
                    <label for="avatar" class="form-label">Foto Profil <span class="font-normal text-text-muted">(opsional, maks 2MB)</span></label>
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="form-input @error('avatar') is-error @enderror">
                    @error('avatar')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-6">Simpan Foto Profil</button>
            </form>

            <div class="divider my-6!"></div>

            <p class="text-sm text-text-secondary mb-4">
                Ingin mengubah nama, email, atau nomor telepon? Data tersebut memerlukan persetujuan Admin.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-4 rounded-xl bg-surface-subtle">
                    <p class="text-label-sm text-text-muted">Nama</p>
                    <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                </div>
                <div class="p-4 rounded-xl bg-surface-subtle">
                    <p class="text-label-sm text-text-muted">Email</p>
                    <p class="text-sm font-semibold truncate">{{ $user->email }}</p>
                </div>
                <div class="p-4 rounded-xl bg-surface-subtle">
                    <p class="text-label-sm text-text-muted">Telepon</p>
                    <p class="text-sm font-semibold">{{ $user->phone_number ?? '—' }}</p>
                </div>
            </div>
            <a href="{{ route('change-requests.index') }}" class="btn btn-outline btn-sm mt-4">Ajukan Perubahan Data</a>
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