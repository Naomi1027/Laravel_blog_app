<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/{userName}/articles/{articleId}', [ArticleController::class, 'show'])->where('articleId', '[0-9]+')->name('articles.show');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::get('/{userName}/articles/{articleId}/edit', [ArticleController::class, 'edit'])->where('articleId', '[0-9]+')->name('articles.edit');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::post('/articles/{articleId}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{articleId}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/{userName}/articles/{articleId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{commentId}/edit', [CommentController::class, 'edit'])->where('commentId', '[0-9]+')->name('comments.edit');
    Route::post('/comments/{commentId}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{commentId}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/articles/{articleId}/like', [ArticleController::class, 'like'])->name('articles.like');
    Route::delete('/articles/{articleId}/like', [ArticleController::class, 'unlike'])->name('articles.unlike');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Googleログインのリダイレクト用ルート
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');

// こっちはGOOGLE_REDIRECT_URLと合わせること
Route::get('https://fdtctvhzsqkvtxcijmjt.supabase.co/auth/v1/callback', [LoginController::class, 'handleGoogleCallback']);


require __DIR__.'/auth.php';
