<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CartPage;
use App\Livewire\Categories;
use App\Livewire\HomePage;
use App\Livewire\MyOrderDetailsPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{product}', ProductDetailPage::class)->name('products.show');
Route::get('/categories', Categories::class)->name('categories');
// Route::get('/categories/{category}', Categories::class)->name('categories.show');
Route::get('/orders', MyOrdersPage::class)->name('orders');
Route::get('/orders/{order}', MyOrderDetailsPage::class)->name('orders.show');
Route::get('cart', CartPage::class)->name('cart');
Route::get('/register', RegisterPage::class)->name('register');
Route::get('/login', LoginPage::class)->name('login');
Route::get('/reset', ResetPasswordPage::class)->name('reset.password');
Route::get('/forgot', ForgotPasswordPage::class)->name('forgot.password');
