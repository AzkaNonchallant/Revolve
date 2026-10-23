<div class="utility-bar" aria-label="Informasi toko">
    <div class="container-app utility-bar-inner">Marketplace fashion <span>Revolve</span></div>
</div>
<header class="navbar">
    <div class="container-app h-full flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center shrink-0" aria-label="Revolve, beranda">
            <span class="revolve-wordmark">Revolve<span>.</span></span>
        </a>

        <nav class="hidden lg:flex items-center gap-6">
            <a href="{{ route('home') }}" class="site-nav-link">Beranda</a>
            <a href="{{ route('products.index') }}" class="site-nav-link">Produk</a>
            @auth
                <a href="{{ route('orders.index') }}" class="site-nav-link">Pesanan</a>
                <a href="{{ route('addresses.index') }}" class="site-nav-link">Alamat</a>
            @endauth
        </nav>

        <div class="flex items-center gap-2">
            <form action="{{ route('products.index') }}" method="GET" class="hidden lg:flex items-center relative">
                <svg class="absolute left-3 w-4 h-4 text-text-muted pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.3-4.3"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                    class="form-input w-48! pl-9! h-10! bg-surface-muted! border-transparent!" aria-label="Cari produk">
            </form>
            <a href="{{ route('products.index') }}" class="mobile-search-link" aria-label="Cari produk">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </a>

            @auth
                @php
                    $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0;
                    $wishlistCount = auth()->user()->wishlist?->items()->count() ?? 0;
                @endphp
                <a href="{{ route('wishlist.index') }}" class="relative w-11 h-11 flex items-center justify-center hover:bg-surface-muted transition-colors" aria-label="Wishlist">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7z"/>
                    </svg>
                    @if ($wishlistCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-primary text-white text-[11px] font-bold flex items-center justify-center">
                            {{ min($wishlistCount, 99) }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('cart.index') }}" class="relative w-11 h-11 flex items-center justify-center hover:bg-surface-muted transition-colors" aria-label="Keranjang">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-primary text-white text-[11px] font-bold flex items-center justify-center">
                            {{ min($cartCount, 99) }}
                        </span>
                    @endif
                </a>

                <div class="relative" data-user-menu>
                    <button type="button" data-user-menu-toggle
                        class="w-11 h-11 flex items-center justify-center rounded-full bg-obsidian text-white text-sm font-bold hover:bg-obsidian-light transition-colors"
                        aria-label="Menu akun" aria-expanded="false">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div data-user-menu-panel class="hidden absolute right-0 mt-2 w-56 card elevation-3 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-surface-muted">
                            <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-text-muted truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Profil</a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Pesanan</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Wishlist</a>
                            <a href="{{ route('addresses.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Alamat</a>
                            <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Notifikasi</a>
                            <a href="{{ auth()->user()->dashboardRoute() }}" class="block px-4 py-2.5 text-sm font-semibold text-primary hover:bg-surface-subtle">Dashboard</a>
                        </div>
                        <div class="py-1 border-t border-surface-muted">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-error hover:bg-error-bg">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm hidden sm:inline-flex">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endauth
            <button type="button" data-mobile-menu-toggle class="lg:hidden min-w-11 min-h-11 flex items-center justify-center hover:bg-surface-muted transition-colors" aria-label="Buka menu" aria-expanded="false">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div data-mobile-menu class="mobile-menu hidden lg:hidden">
        <div class="container-app py-4">
            <a href="{{ route('home') }}" class="mobile-nav-link">Beranda</a>
            <a href="{{ route('products.index') }}" class="mobile-nav-link">Semua produk</a>
            @auth
                <a href="{{ route('orders.index') }}" class="mobile-nav-link">Pesanan</a>
                <a href="{{ route('wishlist.index') }}" class="mobile-nav-link">Wishlist</a>
                <a href="{{ route('addresses.index') }}" class="mobile-nav-link">Alamat</a>
                <a href="{{ route('profile.edit') }}" class="mobile-nav-link">Profil</a>
                <a href="{{ auth()->user()->dashboardRoute() }}" class="mobile-nav-link">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-link">Masuk</a>
                <a href="{{ route('register') }}" class="mobile-nav-link">Daftar</a>
            @endauth
        </div>
    </div>
</header>
