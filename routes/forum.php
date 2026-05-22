<?php

use App\Http\Controllers\Forum\ReplyController;
use App\Http\Controllers\Forum\ThreadController;
use Illuminate\Support\Facades\Route;

// ── Public Forum Routes (readable by everyone) ────────────────────────────────
Route::prefix('forum')->name('forum.')->group(function () {

    Route::get('/', [ThreadController::class, 'index'])->name('index');
    Route::get('/{slug}', [ThreadController::class, 'show'])->name('show');

    // ── Authenticated Routes ──────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        Route::get('/thread/create', [ThreadController::class, 'create'])->name('create');
        Route::post('/', [ThreadController::class, 'store'])->name('store');

        // Threads management
        Route::post('/{thread}/toggle-solved', [ThreadController::class, 'toggleSolved'])->name('toggle-solved');
        Route::post('/{thread}/toggle-closed', [ThreadController::class, 'toggleClosed'])->name('toggle-closed');

        // Replies
        Route::post('/{thread}/replies', [ReplyController::class, 'store'])->name('replies.store');
        Route::post('/{thread}/solved/{reply}', [ReplyController::class, 'markSolved'])->name('solved');
        Route::post('/replies/{reply}/like', [ReplyController::class, 'like'])->name('replies.like');
        Route::delete('/replies/{reply}', [ReplyController::class, 'destroy'])->name('replies.destroy');
    });
});
