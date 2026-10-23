<footer class="bg-obsidian text-white mt-16">
    <div class="container-app py-12 grid grid-cols-1 gap-8 md:grid-cols-3">
        <div>
            <div class="mb-4"><span class="revolve-wordmark text-white">Revolve<span>.</span></span></div>
            <p class="text-sm text-white/60 leading-relaxed">Marketplace fashion untuk menemukan produk yang cocok dengan gayamu.</p>
        </div>
        <div>
            <h3 class="font-heading text-sm font-semibold uppercase tracking-wider mb-4 text-white/80">Navigasi</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="text-white/60 hover:text-white transition-colors">Beranda</a></li>
                <li><a href="{{ route('products.index') }}" class="text-white/60 hover:text-white transition-colors">Produk</a></li>
                @auth
                    <li><a href="{{ route('orders.index') }}" class="text-white/60 hover:text-white transition-colors">Pesanan</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="text-white/60 hover:text-white transition-colors">Profil</a></li>
                @endauth
            </ul>
        </div>
        <div>
            <h3 class="font-heading text-sm font-semibold uppercase tracking-wider mb-4 text-white/80">Akun</h3>
            <ul class="space-y-2 text-sm">
                @auth
                    <li><a href="{{ route('cart.index') }}" class="text-white/60 hover:text-white transition-colors">Keranjang</a></li>
                    <li><a href="{{ route('addresses.index') }}" class="text-white/60 hover:text-white transition-colors">Alamat</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="text-white/60 hover:text-white transition-colors">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="text-white/60 hover:text-white transition-colors">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="container-app py-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Revolve. Semua hak dilindungi.</p>
            <p class="text-xs text-white/40">Dibangun dengan Laravel &amp; Tailwind CSS</p>
        </div>
    </div>
</footer>
