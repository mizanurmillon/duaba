<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

//Login API
Route::controller(LoginController::class)->prefix('users')->group(function () {
    // User Login
    Route::post('/login', 'login');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');
});

// User Profile
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('/onbodding', 'onbodding');
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/logout', 'logout');
    });

    Route::controller(DeliveryController::class)->prefix('delivery')->group(function () {
        Route::post('/create-job', 'createJob');
        Route::get('/stuart/job/{jobId}', 'getJob');
    });
});