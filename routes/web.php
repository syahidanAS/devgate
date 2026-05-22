<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Payment\MidtransController;
use Illuminate\Support\Facades\Route;

// Public Web Welcome Page
Route::get('/', function () {
    return redirect()->route('blog.index');
});

// Unified Dashboard Redirector (Intelligent Multi-Role Portal)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->hasAnyRole(['superadmin', 'author', 'admin-marketplace'])) {
        return redirect()->route('cms.dashboard');
    }
    // Normal customers go to the public home page
    return redirect()->route('blog.index');
})->middleware(['auth', 'verified'])->name('dashboard');


// Midtrans Webhook (Exempted from CSRF)
Route::post('payment/webhook', [MidtransController::class, 'webhook'])->name('payment.webhook');

// Profile & Address management (Authenticated & Verified)
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shipping Address Management
    Route::post('/profile/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/profile/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/profile/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/profile/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.setDefault');

    // RajaOngkir API endpoints for address form dropdowns
    Route::get('/api/provinces', [AddressController::class, 'provinces'])->name('api.provinces');
    Route::get('/api/cities/{provinceId}', [AddressController::class, 'cities'])->name('api.cities');
});

// Public User Notifications (Authenticated only)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
});

// Load Breeze Authentication Routes
require __DIR__.'/auth.php';

// Email Verification Success Page
Route::get('/verified', function () {
    return view('auth.verified-success');
})->middleware('auth')->name('verified.success');

// Newsletter Subscription Route
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Load Modular Domains Route Files
require __DIR__.'/blog.php';
require __DIR__.'/marketplace.php';
require __DIR__.'/cms.php';
require __DIR__.'/forum.php';
