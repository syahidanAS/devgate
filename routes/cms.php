<?php

use App\Http\Controllers\CMS\DashboardController;
use App\Http\Controllers\CMS\ArticleController;
use App\Http\Controllers\CMS\NotificationController;
use App\Http\Controllers\CMS\ProductController;
use App\Http\Controllers\CMS\OrderController;
use App\Http\Controllers\CMS\UserController;
use App\Http\Controllers\CMS\MediaController;
use App\Http\Controllers\CMS\VideoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', '2fa', 'role:superadmin|author|admin-marketplace'])->prefix('cms')->as('cms.')->group(function () {
    
    // Unified Multi-Role Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Notification bell API (all CMS users)
    Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/{id}/read',    [NotificationController::class, 'markRead'])->name('notifications.read');

    // Rich Text Editor Media Uploads
    Route::post('/media/upload', [MediaController::class, 'upload'])
        ->middleware('role_or_permission:superadmin|author|media.upload')
        ->name('media.upload');

    // CMS Articles CRUD (Authors & Superadmins)
    Route::resource('articles', ArticleController::class)
        ->middleware('role_or_permission:superadmin|author');

    // CMS Videos CRUD (Authors & Superadmins)
    Route::middleware('role_or_permission:superadmin|author')->group(function () {
        Route::resource('videos', VideoController::class)->except(['show']);
        Route::patch('videos/{video}/toggle-active',      [VideoController::class, 'toggleActive'])->name('videos.toggleActive');
        Route::patch('videos/{video}/move-up',            [VideoController::class, 'moveUp'])->name('videos.moveUp');
        Route::patch('videos/{video}/move-down',          [VideoController::class, 'moveDown'])->name('videos.moveDown');
        Route::patch('videos/{video}/refresh-thumbnail',  [VideoController::class, 'refreshThumbnail'])->name('videos.refreshThumbnail');
    });

    // CMS Products CRUD (Store Managers & Superadmins)
    Route::middleware('role_or_permission:superadmin|admin-marketplace')->group(function () {
        Route::delete('products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulkDestroy');
        Route::resource('products', ProductController::class);
    });

    // CMS Orders (Store Managers & Superadmins)
    Route::group(['middleware' => 'role_or_permission:superadmin|admin-marketplace'], function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/refund/approve', [OrderController::class, 'approveRefund'])->name('orders.refund.approve');
        Route::post('/orders/{order}/refund/reject', [OrderController::class, 'rejectRefund'])->name('orders.refund.reject');
        Route::get('/orders/{order}/chat', [OrderController::class, 'getMessages'])->name('orders.chat.index');
        Route::post('/orders/{order}/chat', [OrderController::class, 'sendMessage'])->name('orders.chat.send');
    });

    // CMS Users & Roles Administration (Superadmins only)
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('role:superadmin');

    // CMS Chat System (Admins)
    Route::get('/chats/products/search', [\App\Http\Controllers\CMS\ChatSessionController::class, 'searchProducts'])->name('chats.products.search');
    Route::get('/chats', [\App\Http\Controllers\CMS\ChatSessionController::class, 'index'])->name('chats.index');
    Route::get('/chats/{session}/messages', [\App\Http\Controllers\CMS\ChatSessionController::class, 'fetchMessages'])->name('chats.messages');
    Route::post('/chats/{session}/reply', [\App\Http\Controllers\CMS\ChatSessionController::class, 'reply'])->name('chats.reply');
    Route::post('/chats/{session}/close', [\App\Http\Controllers\CMS\ChatSessionController::class, 'close'])->name('chats.close');
});
