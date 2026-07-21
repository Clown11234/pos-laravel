<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;


// ဘာသာစကား ပြောင်းလဲပေးမည့် Route
Route::get('lang/{locale}', [LanguageController::class, 'switchLanguage'])->name('lang.switch');

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

    return redirect()->route('login');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admin & Manager
    Route::middleware(['role:admin,manager'])->group(function () {

        // Product Management Routes
        Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::put('/{product}', 'update')->name('update');
            Route::delete('/{product}', 'destroy')->name('destroy');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/sales/history', [OrderController::class, 'history'])->name('sales.history');
        Route::get('/sales/invoice/{id}', [OrderController::class, 'showInvoice'])->name('sales.invoice.show');

        Route::resource('/sales/suppliers', SupplierController::class)->names([
            'index' => 'sales.suppliers.index',
            'create' => 'sales.suppliers.create',
            'store' => 'sales.suppliers.store',
            'show' => 'sales.suppliers.show',
            'edit' => 'sales.suppliers.edit',
            'update' => 'sales.suppliers.update',
            'destroy' => 'sales.suppliers.destroy',
        ]);
    });

    // Cashier, Admin & Manager Role
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/pos', [ProductController::class, 'pos'])->name('products.pos');
        Route::post('/pos/checkout', [OrderController::class, 'checkout'])->name('pos.checkout');
    });

});
