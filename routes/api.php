<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::post('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])->name('verification.verify');

Route::get('/articles', [ArticleController::class, 'index']);
