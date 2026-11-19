<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderManagementController;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');

Route::middleware('auth')->group(function () {
    Route::get('/catalog/{product}', [CatalogController::class, 'show'])->name('catalog.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{stockItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{stockItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    Route::prefix('home')->name('home.')->group(function () {
        Route::get('/orders/create', [OrderManagementController::class, 'create'])->name('orders.create');
        Route::get('/orders/create/products/{product}', [OrderManagementController::class, 'selectProduct'])->name('orders.select');
        Route::post('/orders/draft', [OrderManagementController::class, 'addDraftItem'])->name('orders.draft.add');
        Route::delete('/orders/draft/{stockItem}', [OrderManagementController::class, 'removeDraftItem'])->name('orders.draft.remove');
        Route::post('/orders', [OrderManagementController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
        Route::delete('/orders/{order}', [OrderManagementController::class, 'destroy'])->name('orders.destroy');
        Route::get('/orders/{order}/edit', [OrderManagementController::class, 'edit'])->name('orders.edit');
        Route::patch('/orders/{order}', [OrderManagementController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}/items/{item}', [OrderManagementController::class, 'destroyItem'])->name('orders.items.destroy');
    });
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
