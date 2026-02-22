<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\GetNotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SaveAddressController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


//Social Login
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);
Route::post('/apple-login', [SocialAuthController::class, 'appleLogin']);

//Register API
Route::controller(RegisterController::class)->prefix('users')->group(function () {
    // User Register
    Route::post('/register', 'Register');
});

//Login API
Route::controller(LoginController::class)->prefix('users')->group(function () {
    // User Login
    Route::post('/login', 'login');

    // Verify Email
    Route::post('/email-verify', 'emailVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    //Reset Password
    Route::post('/reset-password', 'resetPassword');
});

// User Profile
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('/set-name', 'setName');
        Route::post('/set-address', 'setAddress');
        Route::get('/profile', 'profile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/logout', 'logout');
        Route::post('/change-password', 'changePassword');
        Route::delete('/delete-account', 'deleteAccount');
    });

    Route::controller(DeliveryController::class)->prefix('delivery')->group(function () {
        Route::post('/create-job', 'createJob');
        Route::get('/stuart/job/{jobId}', 'getJob');
        Route::get('/stuart/jobs', 'getJobs');
    });

    Route::controller(PaymentController::class)->prefix('payment')->group(function () {
        Route::post('/', 'createStripeCheckout');
        Route::get('/completed/{deliver_job_id}', 'deliveryCompleted');
    });

    Route::controller(SaveAddressController::class)->prefix('address')->group(function () {
        Route::post('/save', 'store');
        Route::get('/list', 'index');
        Route::get('/{addressId}', 'show');
        Route::post('/{addressId}', 'update');
        Route::delete('/{addressId}', 'destroy');
    });

    Route::controller(GetNotificationController::class)->prefix('notification')->group(function () {
        Route::get('/', 'getNotifications');
        Route::get('/{notificationId}', 'removeNotification');
    });
});

Route::controller(PaymentController::class)->group(function () {
    Route::get('/checkout-success', 'checkoutSuccess')->name('checkout.success');
    Route::get('/checkout-cancel', 'checkoutCancel')->name('checkout.cancel');
});
