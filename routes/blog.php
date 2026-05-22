<?php

use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\Blog\CommentController;
use Illuminate\Support\Facades\Route;

// Public Blog Routes
Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [ArticleController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [ArticleController::class, 'tag'])->name('tag');
    Route::get('/author/{username}', [ArticleController::class, 'author'])->name('author');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
    
    // Comments
    Route::post('/{article}/comments', [CommentController::class, 'store'])->name('comments.store');
});
