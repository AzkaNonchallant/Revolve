@extends('layouts.dashboard')

@section('title', 'Ubah Banner — Revolve')
@section('header', 'Ubah Banner')

@section('content')
@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8 max-w-2xl">
    @if ($banner->image)
        <div class="mb-6 rounded-2xl overflow-hidden">
            <img src="{{ asset('storage/' . $banner->image) }}" alt="" class="w-full object-cover max-h-56">
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')
        @include('admin.banners._form', ['banner' => $banner])

        <div class="mt-6 pt-6 border-t border-border">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection