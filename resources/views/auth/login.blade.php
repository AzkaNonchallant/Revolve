@extends('layouts.app')

@section('title', 'Masuk — Revolve')

@section('content')
<div class="min-h-[calc(100vh-68px)] flex items-center justify-center py-10">
    <div class="container-app max-w-6xl">
        <div class="card elevation-1 overflow-hidden grid grid-cols-1 lg:grid-cols-2">
            <div class="hidden lg:flex flex-col justify-between p-10 bg-obsidian text-white relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-10" style="background: radial-gradient(circle, #FF5400 0%, transparent 70%);"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-10">
                        <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </span>
                        <span class="font-heading text-xl font-bold">Revolve</span>
                    </div>
                    <h1 class="text-headline-lg mb-4">Selamat Datang Kembali</h1>
                    <p class="text-white/70 leading-relaxed">Masuk untuk melanjutkan belanja produk kesehatan pilihan Anda.</p>
                </div>
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold">100% Produk Terkurasi</p>
                            <p class="text-xs text-white/60">Kualitas terbaik untuk kesehatan Anda</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 lg:p-12">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-8 lg:hidden">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <h1 class="text-headline-md mb-2">Masuk ke Akun</h1>
                <p class="text-sm text-text-secondary mb-8">Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-semibold hover:text-primary-hover">Daftar di sini</a></p>

                @include('partials.alerts')

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div class="form-group mb-5!">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="form-input @error('email') is-error @enderror" placeholder="nama@email.com">
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mb-2!">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" required
                            class="form-input @error('password') is-error @enderror" placeholder="••••••••">
                        @error('password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-text-secondary">
                            <input type="checkbox" name="remember" id="remember" value="1" class="form-checkbox"
                                @checked(old('remember'))>
                            Ingat saya
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary font-semibold hover:text-primary-hover">Lupa password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">
                        Masuk
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection