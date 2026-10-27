@php
    $primaryNavigation = [
        ['key' => 'men', 'label' => 'MEN'],
        ['key' => 'women', 'label' => 'WOMEN'],
        ['key' => 'kids', 'label' => 'KIDS'],
        ['key' => 'sale', 'label' => 'SALE'],
        ['key' => 'gifts', 'label' => 'GIFTS'],
        ['key' => 'sports', 'label' => 'SPORTS'],
        ['key' => 'trending', 'label' => 'NEW & TRENDING'],
    ];

    $navigationCategoryRoute = static fn (\App\Models\Category $category, string $menuKey) => route(
        'products.index',
        array_filter([
            'category' => $category->id,
            'sort' => $menuKey === 'sale' ? 'discount' : ($menuKey === 'trending' ? 'terlaris' : null),
        ]),
    );
@endphp

<nav class="utility-bar" aria-label="Tautan layanan">
    <div class="utility-bar-inner">
        <a href="{{ route('products.index') }}">Toko</a>
        @auth
            <a href="{{ route('orders.index') }}">Pesanan</a>
            <a href="{{ route('seller-request.create') }}">Jual di Revolve</a>
        @else
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Gabung Revolve</a>
        @endauth
    </div>
</nav>

<header class="navbar" data-site-header>
    <div class="site-header-inner">
        <button type="button" data-mobile-menu-toggle class="mobile-menu-toggle" aria-label="Buka menu" aria-expanded="false" aria-controls="mobile-navigation">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            <span>Menu</span>
        </button>

        <a href="{{ route('home') }}" class="site-header-brand" aria-label="Revolve, beranda">
            <span class="revolve-wordmark">Revolve<span>.</span></span>
        </a>

        <nav class="primary-navigation" aria-label="Kategori utama" data-primary-navigation>
            @foreach ($primaryNavigation as $item)
                <button type="button" class="site-nav-link" data-mega-trigger="{{ $item['key'] }}" aria-controls="desktop-mega-menu" aria-expanded="false">
                    {{ $item['label'] }}
                </button>
            @endforeach
        </nav>

        <div class="header-actions" data-products-url="{{ route('products.index') }}">
            <form action="{{ route('products.index') }}" method="GET" class="header-search-form">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari" aria-label="Cari produk">
            </form>
            @auth
                @php($wishlistCount = auth()->user()->wishlist?->items()->count() ?? 0)
            @endauth
            <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="header-icon-link header-wishlist-link" aria-label="Wishlist{{ auth()->guest() ? ', masuk untuk membuka' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 4.6a5.4 5.4 0 0 0-7.6 0L12 5.8l-1.2-1.2a5.4 5.4 0 0 0-7.6 7.6l1.2 1.2L12 21l7.6-7.6 1.2-1.2a5.4 5.4 0 0 0 0-7.6Z"/></svg>
                <span class="header-action-label">Wishlist</span>
                @auth
                    @if ($wishlistCount > 0)
                        <span class="header-count">{{ min($wishlistCount, 99) }}</span>
                    @endif
                @endauth
            </a>
            <a href="{{ auth()->check() ? route('cart.index') : route('login') }}" class="header-icon-link header-cart-link" aria-label="Keranjang{{ auth()->guest() ? ', masuk untuk membuka' : '' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="19" cy="20" r="1"/></svg>
                <span class="header-action-label">Tas</span>
                @auth
                    @php($cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0)
                    @if ($cartCount > 0)
                        <span class="header-count">{{ min($cartCount, 99) }}</span>
                    @endif
                @endauth
            </a>

            @auth
                <div class="account-menu" data-user-menu>
                    <button type="button" data-user-menu-toggle class="header-icon-link account-menu-toggle" aria-label="Menu akun" aria-expanded="false" aria-controls="account-menu-panel">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                        <span class="header-action-label">Akun</span>
                    </button>
                    <div id="account-menu-panel" data-user-menu-panel class="account-menu-panel hidden">
                        <p class="account-menu-name">{{ auth()->user()->name }}</p>
                        <a href="{{ route('profile.edit') }}">Profil</a>
                        <a href="{{ route('orders.index') }}">Pesanan</a>
                        <a href="{{ route('wishlist.index') }}">Wishlist</a>
                        <a href="{{ route('addresses.index') }}">Alamat</a>
                        <a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="header-icon-link account-login-link" aria-label="Masuk ke akun">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <span class="header-action-label">Akun</span>
                </a>
            @endauth
            <a href="{{ route('products.index') }}" class="mobile-search-link" aria-label="Cari produk">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </a>
        </div>
    </div>

    <section id="desktop-mega-menu" class="mega-menu" data-mega-menu aria-label="Menu kategori" aria-hidden="true" inert>
        <div class="mega-menu-inner">
            <div class="mega-menu-heading">
                <h2 data-mega-menu-title>Kategori Revolve</h2>
                <a href="{{ route('products.index') }}" data-mega-all-link>Semua produk</a>
            </div>
            <div class="mega-menu-columns">
                <div class="mega-menu-column mega-menu-featured">
                    <h3 data-featured-heading>KOLEKSI</h3>
                    <a href="{{ route('products.index') }}" data-featured-link data-featured-first>Koleksi terbaru</a>
                    <a href="{{ route('products.index', ['sort' => 'terlaris']) }}" data-featured-link data-trending-link>Produk terlaris</a>
                    <a href="{{ route('products.index', ['sort' => 'discount']) }}" data-featured-link data-sale-link>Produk diskon</a>
                </div>
                @forelse ($navigationCategories as $category)
                    <div class="mega-menu-column">
                        <a class="mega-menu-category" href="{{ $navigationCategoryRoute($category, 'men') }}" data-category-id="{{ $category->id }}">{{ $category->name }}</a>
                        @foreach ($category->children as $child)
                            <a href="{{ $navigationCategoryRoute($child, 'men') }}" data-category-id="{{ $child->id }}">{{ $child->name }}</a>
                        @endforeach
                    </div>
                @empty
                    <p class="mega-menu-empty">Kategori produk belum tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

    <nav id="mobile-navigation" class="mobile-navigation" data-mobile-menu aria-label="Menu utama" hidden>
        <div class="mobile-navigation-actions">
            <a href="{{ route('products.index') }}">Semua produk</a>
            <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}">Wishlist</a>
            <a href="{{ auth()->check() ? route('cart.index') : route('login') }}">Keranjang</a>
            @auth
                <a href="{{ route('orders.index') }}">Pesanan</a>
                <a href="{{ route('profile.edit') }}">Akun</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Gabung Revolve</a>
            @endauth
        </div>
        <div class="mobile-navigation-groups">
            @foreach ($primaryNavigation as $item)
                <details class="mobile-navigation-group" data-mobile-navigation-group>
                    <summary>{{ $item['label'] }}</summary>
                    <div class="mobile-navigation-links">
                        @if ($item['key'] === 'trending')
                            <a href="{{ route('products.index') }}">Koleksi terbaru</a>
                            <a href="{{ route('products.index', ['sort' => 'terlaris']) }}">Produk terlaris</a>
                        @elseif ($item['key'] === 'sale')
                            <a href="{{ route('home') }}#discounts">Produk diskon</a>
                        @endif
                        @foreach ($navigationCategories as $category)
                            <a href="{{ $navigationCategoryRoute($category, $item['key']) }}">{{ $category->name }}</a>
                            @foreach ($category->children as $child)
                                <a href="{{ $navigationCategoryRoute($child, $item['key']) }}">{{ $child->name }}</a>
                            @endforeach
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    </nav>
</header>
