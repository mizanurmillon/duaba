<?php

use App\Http\Controllers\Web\Frontend\ProfileDeletionController;
use App\Http\Controllers\Web\Frontend\HomeController;
use App\Http\Controllers\Web\Frontend\PageController;
use App\Http\Controllers\Web\Frontend\ResetController;
use Illuminate\Support\Facades\Route;

//! Route for Reset Database and Optimize Clear
Route::get('/reset', [ResetController::class, 'RunMigrations'])->name('reset');
Route::get('/composer', [ResetController::class, 'composer'])->name('composer');
Route::get('/migrate', [ResetController::class, 'migrate'])->name('migrate');
Route::get('/storage', [ResetController::class, 'storage'])->name('storage');

//! Route for Landing Page
Route::get('/', [HomeController::class, 'index'])->name('welcome');

//Dynamic Page
Route::get('/page/terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('dynamicPage.termsAndConditions');
Route::get('/page/legal', [PageController::class, 'legal'])->name('dynamicPage.legal');
Route::get('/page/help', [PageController::class, 'help'])->name('dynamicPage.help');


Route::get('/payment/success', function () {
    return view('frontend.layouts.pages.success');
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return view('frontend.layouts.pages.cancel');
})->name('payment.cancel');

//Profile Deletion Routes________________________________________________________
Route::middleware(['auth'])->group(function () {
    Route::get('/profile-deletion', [ProfileDeletionController::class, 'showAuthenticated'])->name('profile.delete.authenticated');
    Route::delete('/profile-deletion', [ProfileDeletionController::class, 'destroyAuthenticated'])->name('profile.delete.authenticated.destroy');
});