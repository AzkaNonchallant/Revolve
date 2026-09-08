<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\ProductController as WebProductController;
use App\Http\Controllers\Web\CartController as WebCartController;
use App\Http\Controllers\Web\CheckoutController as WebCheckoutController;
use App\Http\Controllers\Web\OrderController as WebOrderController;
use App\Http\Controllers\Web\AddressController as WebAddressController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [WebProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [WebProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [WebProductController::class, 'category'])->name('categories.show');

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

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/cart', [WebCartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [WebCartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cartItem}', [WebCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [WebCartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [WebCartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [WebCheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [WebCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [WebCheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/orders', [WebOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [WebOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [WebOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/addresses', [WebAddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [WebAddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [WebAddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [WebAddressController::class, 'edit'])->name('addresses.edit');
    Route::patch('/addresses/{address}', [WebAddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [WebAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/default', [WebAddressController::class, 'setDefault'])->name('addresses.default');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});