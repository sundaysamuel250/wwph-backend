<?php

use App\Http\Controllers\Employer\JobController;
use App\Http\Controllers\Employer\PaymentController;
use App\Http\Controllers\Employer\SavedCandidateController;
use App\Http\Controllers\Employer\UserController;
use Illuminate\Support\Facades\Route;

// Route::post('/register', 'changePassword');

Route::middleware(['verified', 'jwt.verify', 'auth:api', 'employer'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('/user/change-password', 'changePassword');
        Route::post('/user/delete', 'deleteAccount');
        Route::get('/profile', 'show');
        Route::post('/profile', 'update');
        Route::get('/profile/delete-avatar', 'deleteAvatar');
        Route::post('/profile/social/{id}', 'updateSocial');
        Route::post('/profile/social-delete/{id}', 'deleteSocial');
        Route::post('/profile/social-add', 'addSocial');
    });

    Route::prefix("jobs")->group(function() {
        Route::get('/', [JobController::class, 'index']);
        Route::post('/', [JobController::class, 'store']);
        Route::post('/share/{id}', [JobController::class, 'shareJob']);
        Route::get('/delete/{id}', [JobController::class, 'deleteJob']);
    });

    Route::controller(SavedCandidateController::class)->group(function () {
        Route::get('saved-candidate', 'index');
        Route::post('saved-candidate', 'store');
        Route::get('saved-candidate/delete/{id}', 'destroy');
    });
    Route::controller(PaymentController::class)->group(function () {
        Route::post('fund-wallet', 'makePayment');
        Route::get('transaction', 'transaction');
        Route::post('verify-payment', 'verifyPayment');
        Route::post('withdraw-fund', 'withdraw');
    });
    
});
