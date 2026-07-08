<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController;

// Language Switch လုပ်မည့် Route
Route::get('lang/{locale}', [LanguageController::class, 'switchLanguage'])->name('lang.switch');

Route::get('/pos', [ProductController::class, 'pos'])->name('products.pos');

// show product
Route::get('/', [ProductController::class, 'index'])->name('products.index');
// Product သိမ်းဆည်းရန် POST Route
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

