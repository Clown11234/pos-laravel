<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;


// ဘာသာစကား ပြောင်းလဲပေးမည့် Route
Route::get('lang/{locale}', [LanguageController::class, 'switchLanguage'])->name('lang.switch');

// Guest
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
});

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin() || auth()->user()->isManager()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('products.pos');
    }

    // အကောင့်မဝင်ရသေးလျှင် Login စာမျက်နှာသို့ ပို
    return redirect()->route('login');
});

// Acc ရှိမှ ကြည့်လို့ရ
Route::middleware(['auth'])->group(function () {

    // အကောင့်ပြန်ထွက်ရန် (Logout)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/pos/checkout', [OrderController::class, 'checkout']);

    // Admin & Manager
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/{id}/add-stock', [ProductController::class, 'addStock'])->name('products.add.stock');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/sales/history', [OrderController::class, 'history'])->name('sales.history');
        Route::get('/sales/invoice/{id}', [OrderController::class, 'showInvoice'])->name('sales.invoice.show');
    });

    // Cashier Role
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/pos', [ProductController::class, 'pos'])->name('products.pos');
        Route::post('/pos/checkout', [OrderController::class, 'checkout'])->name('pos.checkout');
    });

});
