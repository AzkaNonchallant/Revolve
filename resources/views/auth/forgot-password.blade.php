@extends('layouts.app')

@section('title', 'Lupa Password — Revolve')

@section('content')
<div class="min-h-[calc(100vh-68px)] flex items-center justify-center py-10">
    <div class="container-app max-w-md">
        <div class="card elevation-1 p-8 lg:p-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-8">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Kembali ke Beranda
            </a>
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h1 class="text-headline-md mb-2">Lupa Password?</h1>
            <p class="text-sm text-text-secondary mb-8">Masukkan email Anda, kami akan mengirimkan tautan untuk mereset password.</p>

            @include('partials.alerts')

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div class="form-group mb-6!">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="form-input @error('email') is-error @enderror" placeholder="nama@email.com">
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full">Kirim Tautan Reset</button>
            </form>

            <p class="text-center text-sm text-text-secondary mt-6">
                Sudah ingat password? <a href="{{ route('login') }}" class="text-primary font-semibold hover:text-primary-hover">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection