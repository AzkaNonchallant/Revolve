@php
    $isAdmin = auth()->user()?->isAdmin();
    $isSeller = auth()->user()?->isSeller();
    $active = $active ?? null;
    $homeRoute = $isAdmin ? route('admin.dashboard') : ($isSeller ? route('seller.dashboard') : route('home'));
    $isDashboardActive = request()->routeIs('admin.dashboard') || request()->routeIs('seller.dashboard');
    $isProfileActive = request()->routeIs('profile.*');
    $isNotificationsActive = request()->routeIs('admin.notifications.*') || request()->routeIs('seller.notifications.*') || request()->routeIs('notifications.*');
@endphp

<aside class="dashboard-sidebar" data-dashboard-sidebar>
    <div class="flex items-center gap-2 px-5 h-16 border-b border-white/10">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </span>
            <div>
                <span class="font-heading text-headline-sm font-bold block leading-none">Revolve</span>
                <span class="text-[11px] text-white/50">
                    {{ $isAdmin ? 'Admin Panel' : ($isSeller ? 'Seller Panel' : 'Akun') }}
                </span>
            </div>
        </a>
        <button type="button" data-dashboard-sidebar-toggle class="lg:hidden ml-auto w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:bg-white/10" aria-label="Tutup menu">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="py-4">
        <p class="side-group-label">Utama</p>

        <a href="{{ $homeRoute }}" class="side-link {{ $isDashboardActive ? 'is-active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
            Dashboard
        </a>

        <a href="{{ route('profile.edit') }}" class="side-link {{ $isProfileActive ? 'is-active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profil
        </a>

        <a href="{{ $isAdmin ? route('admin.notifications.index') : ($isSeller ? route('seller.notifications.index') : route('notifications.index')) }}" class="side-link {{ $isNotificationsActive ? 'is-active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Notifikasi
            @if (auth()->user()->unreadNotifications->isNotEmpty())
                <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-white text-[11px] font-bold">
                    {{ min(auth()->user()->unreadNotifications->count(), 99) }}
                </span>
            @endif
        </a>

        @if ($isSeller)
            <p class="side-group-label">Toko</p>

            <a href="{{ route('seller.shop.edit') }}" class="side-link {{ request()->routeIs('seller.shop.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 12v7h14v-7"/></svg>
                Toko Saya
            </a>

            <a href="{{ route('seller.products.pending') }}" class="side-link {{ request()->routeIs('seller.products.pending') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 18H2M4 18V8l4-4h8l4 4v10"/><circle cx="12" cy="13" r="2"/></svg>
                Produk Menunggu
                @if ($pendingProductsCount = \App\Models\Product::where('approval_status', 'pending')->where('seller_id', auth()->id())->count())
                    <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-white text-[11px] font-bold">{{ $pendingProductsCount }}</span>
                @endif
            </a>

            <a href="{{ route('seller.products.index') }}" class="side-link {{ request()->routeIs('seller.products.create') || request()->routeIs('seller.products.edit') || request()->routeIs('seller.products.index') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Produk Saya
            </a>

            <a href="{{ route('seller.orders.index') }}" class="side-link {{ request()->routeIs('seller.orders.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                Pesanan Toko
            </a>

            <a href="{{ route('seller.sales.index') }}" class="side-link {{ request()->routeIs('seller.sales.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Penjualan & Komisi
            </a>

            <a href="{{ route('seller.statistics.index') }}" class="side-link {{ request()->routeIs('seller.statistics.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                Statistik
            </a>
        @endif

        @if ($isAdmin)
            <p class="side-group-label">Katalog</p>

            <a href="{{ route('admin.products.index') }}" class="side-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Produk
                @if ($pendingApprovals = \App\Models\Product::where('approval_status', 'pending')->count())
                    <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-white text-[11px] font-bold">{{ $pendingApprovals }}</span>
                @endif
            </a>

            <a href="{{ route('admin.categories.index') }}" class="side-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 9h16M9 9v11"/></svg>
                Kategori
            </a>

            <a href="{{ route('admin.banners.index') }}" class="side-link {{ request()->routeIs('admin.banners.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                Banner
            </a>

            <p class="side-group-label">Penjual & Pengguna</p>

            <a href="{{ route('admin.seller-requests.index') }}" class="side-link {{ request()->routeIs('admin.seller-requests.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 16h6M9 12h6"/></svg>
                Permintaan Seller
                @if ($pendingSellerRequests = \App\Models\SellerRequest::where('status', 'pending')->count())
                    <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-white text-[11px] font-bold">{{ $pendingSellerRequests }}</span>
                @endif
            </a>

            <a href="{{ route('admin.sellers.index') }}" class="side-link {{ request()->routeIs('admin.sellers.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 12v7h14v-7"/></svg>
                Seller
            </a>

            <a href="{{ route('admin.users.index') }}" class="side-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Pengguna
            </a>

            <a href="{{ route('admin.change-requests.index') }}" class="side-link {{ request()->routeIs('admin.change-requests.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 5H9l-7 7 7 7h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/><path d="M18 9l-6 6M12 9l6 6"/></svg>
                Perubahan Data
                @if ($pendingChangeRequests = \App\Models\UserChangeRequest::where('status', 'pending')->count())
                    <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-primary text-white text-[11px] font-bold">{{ $pendingChangeRequests }}</span>
                @endif
            </a>

            <p class="side-group-label">Transaksi</p>

            <a href="{{ route('admin.orders.index') }}" class="side-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                Pesanan
            </a>

            <a href="{{ route('admin.payments.index') }}" class="side-link {{ request()->routeIs('admin.payments.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
                Pembayaran
            </a>

            <a href="{{ route('admin.commissions.index') }}" class="side-link {{ request()->routeIs('admin.commissions.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Komisi
            </a>

            <a href="{{ route('admin.vouchers.index') }}" class="side-link {{ request()->routeIs('admin.vouchers.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1a2 2 0 0 0 0 4v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1a2 2 0 0 0 0-4z"/><path d="M14 10v4"/></svg>
                Voucher
            </a>

            <a href="{{ route('admin.reviews.index') }}" class="side-link {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Review
            </a>

            <p class="side-group-label">Lainnya</p>

            <a href="{{ route('admin.reports.index') }}" class="side-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                Laporan
            </a>

            <a href="{{ route('admin.settings.index') }}" class="side-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Pengaturan
            </a>
        @endif

        <p class="side-group-label">Kembali</p>
        <a href="{{ route('home') }}" class="side-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
            Ke Toko
        </a>
    </nav>
</aside>