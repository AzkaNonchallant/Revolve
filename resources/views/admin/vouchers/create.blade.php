@extends('layouts.dashboard')

@section('title', 'Tambah Voucher — Revolve')
@section('header', 'Tambah Voucher')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-5">
        @csrf
        @include('admin.vouchers._form', ['voucher' => null])

        <div class="mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Voucher</button>
        </div>
    </form>
</div>
@endsection