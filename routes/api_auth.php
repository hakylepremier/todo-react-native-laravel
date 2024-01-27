<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => 'auth:sanctum'], function () {
//     //
// });

Route::middleware(['auth:sanctum', 'verified.api'])->get('/sample', function (Request $request) {
    return
        [
            'success' => true,
            'request-user' => $request->user(),
            'auth' => Auth::user(),
            'data' => ['Some data from the database'],
        ];
});

Route::group(['prefix' => 'v1'], function () {
    //

    Route::post(
        '/register',
        [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'register']
    )->name('register');

    Route::post(
        '/login',
        [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'login']
    )->name('login');

    Route::post(
        '/resend/email/token',
        [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'resendPin']
    )->name('resendPin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post(
            'email/verify',
            [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'verifyEmail']
        );
        // Route::middleware('verify.api\V1')->group(function () {
        Route::post(
            '/logout',
            [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'logout']
        );
        // });
    });

    Route::post(
        '/forgot-password',
        [App\Http\Controllers\Api\V1\Auth\ForgotPasswordController::class, 'forgotPassword']
    );

    Route::post(
        '/reset-password',
        [App\Http\Controllers\Api\V1\Auth\ResetPasswordController::class, 'resetPassword']
    );
});
