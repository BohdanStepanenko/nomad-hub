<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CoworkingReviewController;
use App\Http\Controllers\CoworkingSpaceController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\VisaController;
use App\Models\Role;
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

    // Countries
    Route::prefix('countries')->group(function () {
        Route::get('/', [CountryController::class, 'index']);
        Route::get('/{country}', [CountryController::class, 'show']);
    });

    // Visas
    Route::prefix('visas')->group(function () {
        Route::get('/', [VisaController::class, 'index']);
        Route::get('/{visa}', [VisaController::class, 'show']);
    });

    // Coworking Spaces
    Route::prefix('coworking-spaces')->group(function () {
        Route::get('/', [CoworkingSpaceController::class, 'index']);
        Route::get('/{coworkingSpace}', [CoworkingSpaceController::class, 'show']);
    });

    // Coworking Reviews
    Route::prefix('coworking-reviews')->group(function () {
        Route::get('/', [CoworkingReviewController::class, 'index']);
        Route::post('/', [CoworkingReviewController::class, 'store']);
        Route::get('/{coworkingReview}', [CoworkingReviewController::class, 'show']);
        Route::put('/{coworkingReview}', [CoworkingReviewController::class, 'update']);
        Route::delete('/{coworkingReview}', [CoworkingReviewController::class, 'destroy']);
    });

    // Forum Topics
    Route::prefix('forum-topics')->group(function () {
        Route::get('/', [ForumTopicController::class, 'index']);
        Route::post('/', [ForumTopicController::class, 'store']);
        Route::get('/{forumTopic}', [ForumTopicController::class, 'show']);
        Route::put('/{forumTopic}', [ForumTopicController::class, 'update']);
        Route::delete('/{forumTopic}', [ForumTopicController::class, 'destroy']);
    });

    // Admin routes
    Route::middleware(['role:' . implode(',', [Role::ADMIN])])->group(function () {
        Route::prefix('countries')->group(function () {
            Route::post('/', [CountryController::class, 'store']);
            Route::put('/{country}', [CountryController::class, 'update']);
            Route::delete('/{country}', [CountryController::class, 'destroy']);
        });

        Route::prefix('visas')->group(function () {
            Route::post('/', [VisaController::class, 'store']);
            Route::put('/{visa}', [VisaController::class, 'update']);
            Route::delete('/{visa}', [VisaController::class, 'destroy']);
        });

        Route::prefix('coworking-spaces')->group(function () {
            Route::post('/', [CoworkingSpaceController::class, 'store']);
            Route::put('/{coworkingSpace}', [CoworkingSpaceController::class, 'update']);
            Route::delete('/{coworkingSpace}', [CoworkingSpaceController::class, 'destroy']);
        });

        Route::prefix('forum-topics')->group(function () {
            Route::put('/{forumTopic}/lock', [ForumTopicController::class, 'switchLock']);
        });
    });
});
