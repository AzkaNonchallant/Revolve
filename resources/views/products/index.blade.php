@extends('layouts.app')

@section('title', 'Produk - Revolve')

@section('content')
    <div class="container-app catalog-page">
        <nav class="catalog-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span aria-hidden="true">/</span>
            <span>Produk</span>
            @isset($category)
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $category->name }}</span>
            @endisset
        </nav>

        <div class="catalog-heading">
            <div>
                <h1>@isset($category){{ $category->name }}@else Semua Produk @endisset</h1>
                <p>
                    {{ $products->total() }} produk tersedia
                    @if (request('search'))
                        untuk pencarian “{{ request('search') }}”
                    @endif
                </p>
            </div>
            <form action="{{ route('products.index') }}" method="GET" class="catalog-search">
                @foreach (['category', 'size', 'color', 'min_price', 'max_price', 'sort'] as $preserve)
                    @if (request()->filled($preserve))
                        <input type="hidden" name="{{ $preserve }}" value="{{ request($preserve) }}">
                    @endif
                @endforeach
                @foreach ((array) request('brand', []) as $brandId)
                    <input type="hidden" name="brand[]" value="{{ $brandId }}">
                @endforeach
                <label class="sr-only" for="catalog-search">Cari produk</label>
                <input id="catalog-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari produk">
                <button type="submit">Cari</button>
            </form>
        </div>

        @include('partials.alerts')

        @php
            $selectedCategory = $categories->firstWhere('id', request('category'))
                ?? $categories->flatMap->children->firstWhere('id', request('category'));
            $selectedBrands = $brands->whereIn('id', (array) request('brand', []));
            $sortLabels = [
                'price_asc' => 'Harga terendah',
                'terbaru' => 'Terbaru',
                'terlaris' => 'Terlaris',
                'rating' => 'Terlaris',
                'price_desc' => 'Harga tertinggi',
                'discount' => 'Diskon terbesar',
            ];
            $activeSort = request('sort', 'terbaru');
            $hasActiveFilters = request()->filled('category')
                || request()->filled('brand')
                || request()->filled('size')
                || request()->filled('color')
                || request()->filled('min_price')
                || request()->filled('max_price')
                || (request()->filled('sort') && request('sort') !== 'terbaru');
        @endphp

        <div class="catalog-toolbar">
            <p>
                Menampilkan <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                dari {{ $products->total() }} produk
            </p>
            <button type="button" class="catalog-filter-trigger" data-filter-open aria-haspopup="dialog" aria-controls="catalog-filter-drawer">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M7 12h10m-7 5h4"/><circle cx="8" cy="7" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="12" cy="17" r="1.5"/></svg>
                <span>Filter &amp; Sort</span>
                @if ($hasActiveFilters)
                    <span class="catalog-filter-count" aria-label="Filter aktif">{{ collect([request('category'), ...((array) request('brand', [])), request('size'), request('color'), request('min_price'), request('max_price')])->filter()->count() }}</span>
                @endif
            </button>
        </div>

        @if (request('search'))
            <div class="catalog-search-summary">
                <span>Hasil pencarian untuk “{{ request('search') }}”</span>
                <a href="{{ route('products.index', array_filter(['search' => request('search')])) }}">Hapus filter</a>
            </div>
        @endif

        @if ($products->isNotEmpty())
            <div class="grid-products">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="catalog-pagination">{{ $products->links() }}</div>
            @endif
        @else
            <div class="catalog-empty-state">
                <h2>Produk tidak ditemukan</h2>
                <p>Coba ubah kata kunci atau hapus beberapa filter untuk melihat produk lainnya.</p>
                <a href="{{ route('products.index', array_filter(['search' => request('search')])) }}">Lihat produk lainnya</a>
            </div>
        @endif
    </div>

    <div class="filter-drawer-shell" data-filter-shell>
        <button type="button" class="filter-drawer-backdrop" data-filter-close aria-label="Tutup filter"></button>
        <section id="catalog-filter-drawer" class="filter-drawer" data-filter-drawer role="dialog" aria-modal="true" aria-labelledby="filter-drawer-title" aria-hidden="true" inert>
            <header class="filter-drawer-header">
                <h2 id="filter-drawer-title">FILTER &amp; SORT</h2>
                <div class="filter-drawer-header-actions">
                    <a href="{{ route('products.index', array_filter(['search' => request('search')])) }}">Hapus semua</a>
                    <button type="button" data-filter-close aria-label="Tutup Filter & Sort">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </div>
            </header>

            <form id="catalog-filter-form" action="{{ route('products.index') }}" method="GET" class="filter-drawer-form">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="filter-drawer-scroll">
                    <section class="applied-filters" aria-labelledby="applied-filters-title">
                        <h3 id="applied-filters-title">Filter yang dipilih</h3>
                        <div class="applied-filter-tags" data-applied-filters>
                            @if ($selectedCategory)
                                <button type="button" class="applied-filter-tag" data-remove-filter="category" data-filter-value="{{ $selectedCategory->id }}">
                                    {{ $selectedCategory->name }} <span aria-hidden="true">×</span>
                                </button>
                            @endif
                            @foreach ($selectedBrands as $brand)
                                <button type="button" class="applied-filter-tag" data-remove-filter="brand[]" data-filter-value="{{ $brand->id }}">
                                    {{ $brand->name }} <span aria-hidden="true">×</span>
                                </button>
                            @endforeach
                            @if (request('size'))
                                <button type="button" class="applied-filter-tag" data-remove-filter="size">Ukuran {{ request('size') }} <span aria-hidden="true">×</span></button>
                            @endif
                            @if (request('color'))
                                <button type="button" class="applied-filter-tag" data-remove-filter="color">{{ request('color') }} <span aria-hidden="true">×</span></button>
                            @endif
                            @if (request('min_price') || request('max_price'))
                                <button type="button" class="applied-filter-tag" data-remove-filter="price">Harga <span aria-hidden="true">×</span></button>
                            @endif
                            @if ($activeSort !== 'terbaru')
                                <button type="button" class="applied-filter-tag" data-remove-filter="sort">{{ $sortLabels[$activeSort] ?? 'Urutan' }} <span aria-hidden="true">×</span></button>
                            @endif
                            @unless ($hasActiveFilters)
                                <p class="applied-filters-empty">Belum ada filter yang dipilih.</p>
                            @endunless
                        </div>
                    </section>

                    <details class="filter-accordion" open>
                        <summary>Urutkan</summary>
                        <div class="filter-accordion-content filter-option-list">
                            @foreach ([
                                'price_asc' => 'Harga (rendah ke tinggi)',
                                'terbaru' => 'Terbaru',
                                'terlaris' => 'Terlaris',
                                'price_desc' => 'Harga (tinggi ke rendah)',
                                'discount' => 'Diskon terbesar',
                            ] as $sortValue => $sortLabel)
                                <label class="filter-option-row">
                                    <input type="radio" name="sort" value="{{ $sortValue }}" @checked($activeSort === $sortValue || ($sortValue === 'terlaris' && $activeSort === 'rating'))>
                                    <span>{{ $sortLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="filter-accordion" open>
                        <summary>Kategori</summary>
                        <div class="filter-accordion-content filter-option-list">
                            @foreach ($categories as $cat)
                                <label class="filter-option-row">
                                    <input type="radio" name="category" value="{{ $cat->id }}" @checked((string) request('category') === (string) $cat->id)>
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="filter-accordion" open>
                        <summary>Brand</summary>
                        <div class="filter-accordion-content filter-option-list">
                            @foreach ($brands as $brand)
                                <label class="filter-option-row">
                                    <input type="checkbox" name="brand[]" value="{{ $brand->id }}" @checked(in_array($brand->id, (array) request('brand', [])))>
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="filter-accordion" open>
                        <summary>Ukuran</summary>
                        <div class="filter-accordion-content filter-size-options">
                            @foreach ($sizes as $size)
                                <button type="button" data-size-toggle data-value="{{ $size }}" class="filter-size-option {{ request('size') === $size ? 'is-selected' : '' }}" aria-pressed="{{ request('size') === $size ? 'true' : 'false' }}">{{ $size }}</button>
                            @endforeach
                            <input type="hidden" name="size" value="{{ request('size') }}" data-filter-size-value>
                        </div>
                    </details>

                    <details class="filter-accordion">
                        <summary>Warna</summary>
                        <div class="filter-accordion-content filter-option-list">
                            @foreach ($colors as $color)
                                <label class="filter-option-row">
                                    <input type="radio" name="color" value="{{ $color }}" @checked(request('color') === $color)>
                                    <span>{{ $color }}</span>
                                </label>
                            @endforeach
                        </div>
                    </details>

                    <details class="filter-accordion">
                        <summary>Rentang harga</summary>
                        <div class="filter-accordion-content filter-price-range">
                            <label>Minimum
                                <input type="number" name="min_price" value="{{ request('min_price') }}" min="0" inputmode="numeric" placeholder="Rp">
                            </label>
                            <label>Maksimum
                                <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" inputmode="numeric" placeholder="Rp">
                            </label>
                        </div>
                    </details>
                </div>

                <footer class="filter-drawer-footer">
                    <p>{{ $products->total() }} produk ditemukan</p>
                    <button type="submit">Tampilkan produk</button>
                </footer>
            </form>
        </section>
    </div>
@endsection
