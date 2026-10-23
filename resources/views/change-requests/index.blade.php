@extends('layouts.app')

@section('title', 'Perubahan Data — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <h1 class="text-headline-lg mb-8">Permintaan Perubahan Data</h1>

    @include('partials.alerts')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        {{-- Form --}}
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="text-headline-sm mb-2">Ajukan Perubahan</h2>
            <p class="text-sm text-text-secondary mb-6">
                Perubahan nama, email, atau nomor telepon harus disetujui Admin terlebih dahulu.
            </p>
            <form action="{{ route('change-requests.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="field" class="form-label">Data yang Diubah <span class="text-error">*</span></label>
                    <select name="field" id="field" class="form-input @error('field') is-error @enderror">
                        <option value="">— Pilih Data —</option>
                        @foreach (\App\Models\UserChangeRequest::FIELDS as $value => $label)
                            <option value="{{ $value }}" {{ old('field') === $value ? 'selected' : '' }}>
                                {{ $label }} (saat ini: {{ auth()->user()->{$value} ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('field')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="new_value" class="form-label">Nilai Baru <span class="text-error">*</span></label>
                    <input type="text" name="new_value" id="new_value" value="{{ old('new_value') }}"
                        class="form-input @error('new_value') is-error @enderror" placeholder="Nilai pengganti...">
                    @error('new_value')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="form-label">Alasan Perubahan <span class="text-error">*</span></label>
                    <textarea name="reason" id="reason" rows="3"
                        class="form-input @error('reason') is-error @enderror"
                        placeholder="Jelaskan alasan Anda mengajukan perubahan...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full">Kirim Permintaan</button>
            </form>
        </div>

        {{-- History --}}
        <div class="card elevation-1 bg-surface p-6 lg:p-8">
            <h2 class="text-headline-sm mb-6">Riwayat Permintaan</h2>
            @if ($requests->isNotEmpty())
                <div class="divide-y divide-surface-muted">
                    @foreach ($requests as $request)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-sm font-semibold">{{ $request->field_label }}</p>
                                <span class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'rejected' ? 'badge-error' : 'badge-warning') }}">
                                    {{ \App\Models\UserChangeRequest::statusLabel($request->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-text-secondary space-y-0.5">
                                <p><span class="text-text-muted">Lama:</span> <span class="line-through">{{ $request->old_value_display }}</span></p>
                                <p><span class="text-text-muted">Baru:</span> <span class="font-semibold">{{ $request->new_value_display }}</span></p>
                                <p class="text-xs text-text-muted">{{ $request->created_at->translatedFormat('d M Y, H:i') }}</p>
                                @if ($request->status === 'rejected' && $request->admin_note)
                                    <p class="text-xs text-error mt-1">Alasan penolakan: {{ $request->admin_note }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($requests->hasPages())
                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><path d="M18 9l-6 6M12 9l6 6"/></svg>
                    <p>Belum ada permintaan perubahan data.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection