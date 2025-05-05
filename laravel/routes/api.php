<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

    Route::prefix('google')->group(function () {
        Route::get('/redirect', [GoogleAuthController::class, 'redirect']);
        Route::get('/callback', [GoogleAuthController::class, 'callback']);
    });

    Route::prefix('discord')->group(function () {
        Route::get('/redirect', [DiscordAuthController::class, 'redirect']);
        Route::get('/callback', [DiscordAuthController::class, 'callback']);
    });

    Route::prefix('password')->group(function () {
        Route::post('/forgot', [PasswordResetController::class, 'sendResetLink']);
        Route::post('/reset', [PasswordResetController::class, 'resetPassword']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'getProfileInfo']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
        Route::put('/name', [ProfileController::class, 'updateName']);
        Route::put('/email', [ProfileController::class, 'updateEmail']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::post('/logout', [ProfileController::class, 'logout']);
    });
});
