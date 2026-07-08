<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController; // Auth အတွက် Controller အသစ်

// လူတိုင်းကြည့်လို့ရ

// ၁။ ဘာသာစကား ပြောင်းလဲပေးမည့် Route
Route::get('lang/{locale}', [LanguageController::class, 'switchLanguage'])->name('lang.switch');

// ၂။ Guest (အကောင့်မဝင်ရသေးသူများ) သာ ဝင်ခွင့်ရှိမည့် Login Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
});

// ၃။ အကယ်၍ ဝဘ်ဆိုက် root (/) ကိုနှိပ်လျှင် Login စာမျက်နှာသို့ တန်းပို့ရန်
Route::get('/', function () {
    return redirect()->route('login');
});

// Acc ရှိရင်ကြည့်လို့ရတယ်
Route::middleware(['auth'])->group(function () {

    // အကောင့်ပြန်ထွက်ရန် (Logout)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admin & Manager
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Cashier Role
    Route::middleware(['role:admin,manager,cashier'])->group(function () {
        Route::get('/pos', [ProductController::class, 'pos'])->name('products.pos');
    });

});
