<header class="navbar">
    <div class="container-app h-full flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </span>
            <span class="font-heading text-headline-sm font-bold">Revolve</span>
        </a>

        <nav class="hidden md:flex items-center gap-1">
            <a href="{{ route('home') }}" class="text-sm font-medium px-4 py-2 rounded-full hover:bg-surface-muted transition-colors">Beranda</a>
            <a href="{{ route('products.index') }}" class="text-sm font-medium px-4 py-2 rounded-full hover:bg-surface-muted transition-colors">Produk</a>
            @auth
                <a href="{{ route('orders.index') }}" class="text-sm font-medium px-4 py-2 rounded-full hover:bg-surface-muted transition-colors">Pesanan</a>
                <a href="{{ route('addresses.index') }}" class="text-sm font-medium px-4 py-2 rounded-full hover:bg-surface-muted transition-colors">Alamat</a>
            @endauth
        </nav>

        <div class="flex items-center gap-2">
            <form action="{{ route('products.index') }}" method="GET" class="hidden lg:flex items-center relative">
                <svg class="absolute left-3 w-4 h-4 text-text-muted pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.3-4.3"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                    class="form-input w-56! pl-9! h-10! rounded-full! bg-surface-muted! border-transparent!" aria-label="Cari produk">
            </form>

            @auth
                @php
                    $cartCount = auth()->user()->cart?->items()->count() ?? 0;
                @endphp
                <a href="{{ route('cart.index') }}" class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-muted transition-colors" aria-label="Keranjang">
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
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-obsidian text-white text-sm font-bold hover:bg-obsidian-light transition-colors"
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
                            <a href="{{ route('addresses.index') }}" class="block px-4 py-2.5 text-sm hover:bg-surface-subtle">Alamat</a>
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
            <button type="button" data-mobile-menu-toggle class="md:hidden w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-muted transition-colors" aria-label="Menu">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div data-mobile-menu class="hidden md:hidden bg-white/95 backdrop-blur border-b border-surface-muted">
        <div class="container-app py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Beranda</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Produk</a>
            @auth
                <a href="{{ route('orders.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Pesanan</a>
                <a href="{{ route('addresses.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Alamat</a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Profil</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Masuk</a>
                <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-surface-muted">Daftar</a>
            @endauth
        </div>
    </div>
</header>