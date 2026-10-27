@extends('layouts.dashboard')

@section('title', 'Tambah Banner — Revolve')
@section('header', 'Tambah Banner')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @include('admin.banners._form', ['banner' => null])

        <div class="mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Banner</button>
        </div>
    </form>
</div>
@endsection