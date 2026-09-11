@extends('layouts.app')

@section('title', 'Ubah Alamat — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16 max-w-3xl">
    <a href="{{ route('addresses.index') }}" class="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Alamat
    </a>

    <h1 class="text-headline-lg mb-6">Ubah Alamat</h1>

    <div class="card elevation-1 bg-surface p-6 lg:p-8">
        <form method="POST" action="{{ route('addresses.update', $address) }}" class="w-full">
            @csrf
            @method('PATCH')
            @include('partials.address-form', ['address' => $address])
            <div class="flex flex-col sm:flex-row gap-3 mt-8">
                <button type="submit" class="btn btn-primary flex-grow sm:flex-grow-0">Simpan Perubahan</button>
                <a href="{{ route('addresses.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection