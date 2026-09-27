<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\Admin\CategoryController;

// ==================== GUEST ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
});

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // POS — accessible to all logged-in roles
    Route::get('/pos', [ProductController::class, 'pos'])->name('pos');
    Route::post('/pos/add/{id}', [ProductController::class, 'addToCart'])->name('pos.add');
    Route::post('/pos/remove/{id}', [ProductController::class, 'removeFromCart'])->name('pos.remove');
    Route::get('/checkout', [ProductController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [ProductController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/receipt/{id}', [ProductController::class, 'receipt'])->name('receipt');
    Route::get('/transactions', [ProductController::class, 'transactionHistory'])->name('transaction.history');

    // Admin + Manager: Products & Categories
    Route::middleware('role:admin,manager')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Admin only: User management
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

});