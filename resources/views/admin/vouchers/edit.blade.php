@extends('layouts.dashboard')

@section('title', 'Ubah Voucher — Revolve')
@section('header', 'Ubah Voucher: {{ $voucher->code }}')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        @include('admin.vouchers._form', ['voucher' => $voucher])

        <div class="mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection