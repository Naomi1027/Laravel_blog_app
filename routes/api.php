<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
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

Route::post('/email/verify', [VerifyEmailController::class, 'verifyEmail'])->name('verification.verify');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LogoutController::class, 'logout']);

Route::get('/articles', [ArticleController::class, 'index']);
