@extends('layouts.dashboard')

@section('title', 'Voucher — Revolve')
@section('header', 'Kelola Voucher')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-text-muted">Kode voucher dikonversi ke huruf besar otomatis.</p>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Voucher
    </a>
</div>

@include('partials.alerts')

<div class="card elevation-1 bg-surface p-6 lg:p-8">
    @if ($vouchers->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="text-left text-text-muted border-b border-border">
                        <th class="py-3 pr-4 font-medium">Kode</th>
                        <th class="py-3 pr-4 font-medium">Nama</th>
                        <th class="py-3 pr-4 font-medium">Diskon</th>
                        <th class="py-3 pr-4 font-medium">Cakupan</th>
                        <th class="py-3 pr-4 font-medium">Berlaku</th>
                        <th class="py-3 pr-4 font-medium">Pemakaian</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th class="py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vouchers as $voucher)
                        <tr class="border-b border-border">
                            <td class="py-3 pr-4">
                                <span class="badge badge-primary">{{ $voucher->code }}</span>
                            </td>
                            <td class="py-3 pr-4">{{ $voucher->name }}</td>
                            <td class="py-3 pr-4">
                                {{ $voucher->type === 'percent' ? $voucher->value . '%' : 'Rp ' . number_format((float) $voucher->value, 0, ',', '.') }}
                                <p class="text-xs text-text-muted">Min. Rp {{ number_format((float) $voucher->min_purchase, 0, ',', '.') }}</p>
                            </td>
                            <td class="py-3 pr-4 text-xs">
                                {{ $voucher->scope === 'marketplace' ? 'Marketplace' : ($voucher->scope === 'category' ? ('Kategori: ' . ($voucher->category?->name ?? '—')) : ('Seller: ' . ($voucher->seller?->name ?? '—'))) }}
                            </td>
                            <td class="py-3 pr-4 text-xs">
                                {{ $voucher->starts_at?->translatedFormat('d M Y') ?? 'Mulai kapan saja' }}
                                → {{ $voucher->ends_at?->translatedFormat('d M Y') ?? 'Selamanya' }}
                            </td>
                            <td class="py-3 pr-4 text-xs">
                                {{ $voucher->used_count }} / {{ $voucher->quota ?? '∞' }}
                            </td>
                            <td class="py-3 pr-4">
                                <span class="badge {{ $voucher->is_active ? 'badge-success' : 'badge-error' }}">{{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    <form action="{{ route('admin.vouchers.toggle', $voucher) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm text-error">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $vouchers->links() }}
        </div>
    @else
        <div class="empty-state"><p>Belum ada voucher.</p></div>
    @endif
</div>
@endsection