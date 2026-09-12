@extends('layouts.dashboard')

@section('title', 'Pengaturan — Revolve')
@section('header', 'Pengaturan Marketplace')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    <h2 class="form-label mb-1">Komisi Marketplace</h2>
    <p class="text-xs text-text-muted mb-6">Persentase komisi yang dipotong dari setiap transaksi seller yang berhasil.</p>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="commission_rate" class="form-label">Tarif Komisi (%)</label>
            <div class="flex items-center gap-3">
                <input type="number" name="commission_rate" id="commission_rate" min="0" max="100" step="0.1" value="{{ old('commission_rate', $settings['commission_rate'] ?? 5) }}" class="form-input sm:w-40 @error('commission_rate') is-error @enderror">
                <span class="text-sm text-text-secondary">%</span>
            </div>
            @error('commission_rate')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection