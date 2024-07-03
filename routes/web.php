<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
Route::post('/articles/{articleId}', [ArticleController::class, 'update'])->name('articles.update');
Route::delete('/articles/{articleId}', [ArticleController::class, 'destroy'])->name('articles.destroy');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::get('/{userName}/articles/{articleId}/edit', [ArticleController::class, 'edit'])->where('articleId', '[0-9]+')->name('articles.edit');
    Route::get('/{articleId}/comments/{commentId}/edit', [CommentController::class, 'edit'])->where(['articleId' => '[0-9]+', 'commentId' => '[0-9]+'])->name('comments.edit');
});

Route::post('/{articleId}/comments/store', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comments/{commentId}/update', [CommentController::class, 'update'])->name('comments.update');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
