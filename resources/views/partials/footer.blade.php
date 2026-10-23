<footer class="site-footer">
    <div class="container-app footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="revolve-wordmark text-white">Revolve<span>.</span></a>
            <p>Marketplace fashion Revolve.</p>
        </div>
        <div class="footer-column">
            <h2>Belanja</h2>
            <a href="{{ route('products.index') }}">Semua produk</a>
            <a href="{{ route('products.index', ['sort' => 'discount']) }}">Produk diskon</a>
        </div>
        <div class="footer-column">
            <h2>Akun</h2>
            @auth
                <a href="{{ route('orders.index') }}">Pesanan</a>
                <a href="{{ route('wishlist.index') }}">Wishlist</a>
                <a href="{{ route('profile.edit') }}">Profil</a>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>
        <div class="footer-column">
            <h2>Seller</h2>
            @auth
                <a href="{{ route('seller-request.create') }}">Buka toko</a>
            @else
                <a href="{{ route('register') }}">Daftar sebagai seller</a>
            @endauth
            <a href="{{ route('home') }}">Beranda Revolve</a>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container-app flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Revolve. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>
