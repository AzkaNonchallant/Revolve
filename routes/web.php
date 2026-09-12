<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\SellerRequestController as AdminSellerRequestController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserChangeRequestController as AdminUserChangeRequestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\NotificationController as SellerNotificationController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\SalesController as SellerSalesController;
use App\Http\Controllers\Seller\ShopController as SellerShopController;
use App\Http\Controllers\Seller\StatisticsController as SellerStatisticsController;
use App\Http\Controllers\Web\AddressController as WebAddressController;
use App\Http\Controllers\Web\CartController as WebCartController;
use App\Http\Controllers\Web\CheckoutController as WebCheckoutController;
use App\Http\Controllers\Web\NotificationController as WebNotificationController;
use App\Http\Controllers\Web\OrderController as WebOrderController;
use App\Http\Controllers\Web\ProductController as WebProductController;
use App\Http\Controllers\Web\ReviewController as WebReviewController;
use App\Http\Controllers\Web\SellerRequestController as WebSellerRequestController;
use App\Http\Controllers\Web\UserChangeRequestController as WebUserChangeRequestController;
use App\Http\Controllers\Web\WishlistController as WebWishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [WebProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [WebProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [WebProductController::class, 'category'])->name('categories.show');
Route::get('/shops/{shop:slug}', [WebProductController::class, 'shop'])->name('shops.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/cart', [WebCartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [WebCartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cartItem}', [WebCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [WebCartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [WebCartController::class, 'clear'])->name('cart.clear');
    Route::patch('/cart/{cartItem}/select', [WebCartController::class, 'toggleSelect'])->name('cart.select');

    Route::get('/checkout', [WebCheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [WebCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [WebCheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/orders', [WebOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [WebOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [WebOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/pay', [WebOrderController::class, 'pay'])->name('orders.pay');
    Route::post('/orders/{order}/complete', [WebOrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/items/{orderItem}/review', [WebReviewController::class, 'store'])->name('orders.review.store');

    Route::get('/addresses', [WebAddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [WebAddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [WebAddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [WebAddressController::class, 'edit'])->name('addresses.edit');
    Route::patch('/addresses/{address}', [WebAddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [WebAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/default', [WebAddressController::class, 'setDefault'])->name('addresses.default');

    Route::get('/wishlist', [WebWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WebWishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlistItem}', [WebWishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{wishlistItem}/move-to-cart', [WebWishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

    Route::get('/seller-request', [WebSellerRequestController::class, 'create'])->name('seller-request.create');
    Route::post('/seller-request', [WebSellerRequestController::class, 'store'])->name('seller-request.store');
    Route::get('/seller-requests', [WebSellerRequestController::class, 'index'])->name('seller-request.index');

    Route::get('/change-requests', [WebUserChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::post('/change-requests', [WebUserChangeRequestController::class, 'store'])->name('change-requests.store');

    Route::get('/notifications', [WebNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [WebNotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller', 'active'])->group(function () {
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/shop/edit', [SellerShopController::class, 'edit'])->name('shop.edit');
    Route::put('/shop', [SellerShopController::class, 'update'])->name('shop.update');

    Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');
    Route::get('/products/pending', [SellerProductController::class, 'pending'])->name('products.pending');
    Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');
    Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/submit', [SellerProductController::class, 'submitForApproval'])->name('products.submit');

    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/sales', [SellerSalesController::class, 'index'])->name('sales.index');
    Route::get('/statistics', [SellerStatisticsController::class, 'index'])->name('statistics.index');

    Route::get('/notifications', [SellerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [SellerNotificationController::class, 'readAll'])->name('notifications.read-all');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'active'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
    Route::get('/sellers/{seller}', [AdminSellerController::class, 'show'])->name('sellers.show');
    Route::post('/sellers/{seller}/suspend', [AdminSellerController::class, 'suspend'])->name('sellers.suspend');

    Route::get('/seller-requests', [AdminSellerRequestController::class, 'index'])->name('seller-requests.index');
    Route::get('/seller-requests/{sellerRequest}', [AdminSellerRequestController::class, 'show'])->name('seller-requests.show');
    Route::post('/seller-requests/{sellerRequest}/approve', [AdminSellerRequestController::class, 'approve'])->name('seller-requests.approve');
    Route::post('/seller-requests/{sellerRequest}/reject', [AdminSellerRequestController::class, 'reject'])->name('seller-requests.reject');

    Route::get('/change-requests', [AdminUserChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::get('/change-requests/{changeRequest}', [AdminUserChangeRequestController::class, 'show'])->name('change-requests.show');
    Route::post('/change-requests/{changeRequest}/approve', [AdminUserChangeRequestController::class, 'approve'])->name('change-requests.approve');
    Route::post('/change-requests/{changeRequest}/reject', [AdminUserChangeRequestController::class, 'reject'])->name('change-requests.reject');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{product}/reject', [AdminProductController::class, 'reject'])->name('products.reject');
    Route::post('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');

    Route::get('/commissions', [AdminCommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions/{commission}/pay', [AdminCommissionController::class, 'pay'])->name('commissions.pay');

    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [AdminVoucherController::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{voucher}/edit', [AdminVoucherController::class, 'edit'])->name('vouchers.edit');
    Route::put('/vouchers/{voucher}', [AdminVoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/vouchers/{voucher}', [AdminVoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::post('/vouchers/{voucher}/toggle', [AdminVoucherController::class, 'toggle'])->name('vouchers.toggle');

    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
