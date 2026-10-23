@extends('layouts.app')

@section('title', 'Produk — Revolve')

@section('content')
<div class="container-app pt-8 lg:pt-12 pb-16">
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-text-primary">Beranda</a>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-text-primary font-medium">Produk</span>
        @isset($category)
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            <span class="text-text-primary font-medium">{{ $category->name }}</span>
        @endisset
    </nav>

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-headline-lg mb-2">
                @isset($category)
                    {{ $category->name }}
                @else
                    Semua Produk
                @endisset
            </h1>
            <p class="text-text-secondary text-sm">
                {{ $products->total() }} produk tersedia
                @if(request('search'))
                    untuk pencarian &quot;{{ request('search') }}&quot;
                @endif
            </p>
        </div>
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
            @foreach (['category', 'size', 'color', 'min_price', 'max_price', 'sort'] as $preserve)
                @if (request()->filled($preserve))
                    <input type="hidden" name="{{ $preserve }}" value="{{ request($preserve) }}">
                @endif
            @endforeach
            @foreach ((array) request('brand', []) as $br)
                <input type="hidden" name="brand[]" value="{{ $br }}">
            @endforeach
            <div class="relative flex-grow lg:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.3-4.3"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                    class="form-input pl-9!" aria-label="Cari produk">
            </div>
            <button type="submit" class="btn btn-secondary shrink-0">Cari</button>
        </form>
    </div>

    @include('partials.alerts')

    <div class="mb-6 lg:hidden">
    <button type="button" data-mobile-filter-open class="btn btn-outline w-full">Filter & Urutkan</button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-8">
        {{-- Filter sidebar --}}
        <aside class="hidden lg:block">
            <form action="{{ route('products.index') }}" method="GET" class="card elevation-1 bg-surface p-5 lg:sticky lg:top-24 space-y-6">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <h2 class="text-title-md font-semibold">Filter</h2>

                <div>
                    <p class="text-label-sm text-text-muted mb-2">Kategori</p>
                    <div class="space-y-1.5">
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value="{{ $cat->id }}" onchange="this.form.submit()"
                                    class="accent-[#FF5400]" {{ request()->query('category') == $cat->id ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-label-sm text-text-muted mb-2">Brand</p>
                    <div class="space-y-1.5">
                        @foreach ($brands as $brand)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="brand[]" value="{{ $brand->id }}" onchange="this.form.submit()"
                                    class="accent-[#FF5400]" {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}>
                                {{ $brand->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-label-sm text-text-muted mb-2">Ukuran</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($sizes as $size)
                            <button type="button" data-size-toggle value="{{ $size }}"
                                class="variant-option {{ request('size') === $size ? 'is-selected' : '' }}">{{ $size }}</button>
                        @endforeach
                        <input type="hidden" id="size-input" name="size" value="{{ request('size') }}">
                    </div>
                </div>

                <div>
                    <p class="text-label-sm text-text-muted mb-2">Warna</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($colors as $color)
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="color" value="{{ $color }}" class="accent-[#FF5400]" {{ request('color') === $color ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-xs font-medium">{{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-label-sm text-text-muted mb-2">Harga</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="form-input text-sm!" min="0">
                        <span class="text-text-muted">–</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks" class="form-input text-sm!" min="0">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow">Terapkan</button>
                    <a href="{{ route('products.index') }}" class="text-sm text-text-muted hover:text-text-primary">Reset</a>
                </div>
            </form>
        </aside>

        {{-- Products --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-text-secondary">
                    Menampilkan <span class="font-semibold text-text-primary">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span> dari {{ $products->total() }}
                </p>
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-1">
                    @foreach (['search', 'category', 'min_price', 'max_price', 'size', 'color'] as $preserve)
                        @if (request()->filled($preserve))
                            <input type="hidden" name="{{ $preserve }}" value="{{ request($preserve) }}">
                        @endif
                    @endforeach
                    @foreach ((array) request('brand', []) as $br)
                        <input type="hidden" name="brand[]" value="{{ $br }}">
                    @endforeach
                    <label for="sort" class="sr-only">Urutkan</label>
                    <select id="sort" name="sort" onchange="this.form.submit()" class="form-input text-sm! py-2! rounded-lg!">
                        <option value="terbaru" {{ request('sort') === 'terbaru' || ! request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlaris" {{ request('sort') === 'terlaris' || request('sort') === 'rating' ? 'selected' : '' }}>Terlaris</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="discount" {{ request('sort') === 'discount' ? 'selected' : '' }}>Diskon Terbesar</option>
                    </select>
                </form>
            </div>

            @if(request('search'))
                <div class="mb-6 flex items-center gap-2">
                    <p class="text-sm text-text-secondary">
                        Hasil pencarian untuk &quot;<span class="font-semibold text-text-primary">{{ request('search') }}</span>&quot;
                    </p>
                    <a href="{{ route('products.index') }}" class="text-sm text-primary font-semibold hover:text-primary-hover">Reset</a>
                </div>
            @endif

            @if ($products->isNotEmpty())
                <div class="grid-products">
                    @foreach ($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state card elevation-1 bg-surface">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.3-4.3"/>
                    </svg>
                    <h3 class="text-title-md mb-2">Produk tidak ditemukan</h3>
                    <p>Tidak ada produk yang cocok dengan pencarian Anda.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Lihat Semua Produk</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Mobile filter --}}
<div data-mobile-filter-panel class="fixed inset-0 z-50 hidden lg:hidden">
    <div class="absolute inset-0 bg-black/50" data-mobile-filter-close></div>
    <div class="absolute left-0 top-0 bottom-0 w-[300px] max-w-[85vw] bg-white overflow-y-auto">
        <div class="flex items-center justify-between px-5 h-16 border-b border-surface-muted">
            <h2 class="text-title-md font-semibold">Filter Produk</h2>
            <button type="button" data-mobile-filter-close class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-muted" aria-label="Tutup">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <form action="{{ route('products.index') }}" method="GET" class="space-y-6" id="mobile-filter-form">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div>
                    <p class="text-label-sm text-text-muted mb-2">Kategori</p>
                    <div class="space-y-1.5">
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value="{{ $cat->id }}"
                                    class="accent-[#FF5400]" {{ request()->query('category') == $cat->id ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-label-sm text-text-muted mb-2">Brand</p>
                    <div class="space-y-1.5">
                        @foreach ($brands as $brand)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                                    class="accent-[#FF5400]" {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}>
                                {{ $brand->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-label-sm text-text-muted mb-2">Harga</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="form-input text-sm!" min="0">
                        <span class="text-text-muted">–</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks" class="form-input text-sm!" min="0">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary flex-grow">Terapkan</button>
                    <a href="{{ route('products.index') }}" class="text-sm text-text-muted hover:text-text-primary">Reset</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-size-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById('size-input');
            if (input.value === btn.dataset.value) {
                input.value = '';
                btn.classList.remove('is-selected');
            } else {
                input.value = btn.dataset.value;
                document.querySelectorAll('[data-size-toggle]').forEach((b) => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
            }
        });
    });

    const panel = document.querySelector('[data-mobile-filter-panel]');
    const open = document.querySelector('[data-mobile-filter-open]');
    const close = document.querySelectorAll('[data-mobile-filter-close]');
    if (panel && open) {
        open.addEventListener('click', () => panel.classList.remove('hidden'));
        close.forEach((el) => el.addEventListener('click', () => panel.classList.add('hidden')));
    }
});
</script>
@endpush