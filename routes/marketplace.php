<?php

use App\Http\Controllers\Marketplace\ProductController;
use App\Http\Controllers\Marketplace\CartController;
use App\Http\Controllers\Marketplace\CheckoutController;
use App\Http\Controllers\Marketplace\OrderController;
use Illuminate\Support\Facades\Route;

// Public Product Catalog
Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// Cart Routes
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

// Checkout & Orders (Requires Authentication, Verification, and 2FA)
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/address', [CheckoutController::class, 'storeAddress'])->name('address.store');
        Route::get('/cities/{province_id}', [CheckoutController::class, 'getCities'])->name('cities');
        Route::post('/ongkir', [CheckoutController::class, 'checkOngkir'])->name('ongkir');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{number}', [OrderController::class, 'show'])->name('show');
        Route::post('/{number}/complete', [OrderController::class, 'complete'])->name('complete');
        Route::post('/{number}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{number}/verify', [OrderController::class, 'verify'])->name('verify');
        Route::post('/{number}/refund', [OrderController::class, 'refund'])->name('refund.store');
        Route::get('/{number}/chat', [OrderController::class, 'getMessages'])->name('chat.index');
        Route::post('/{number}/chat', [OrderController::class, 'sendMessage'])->name('chat.send');
    });
});
