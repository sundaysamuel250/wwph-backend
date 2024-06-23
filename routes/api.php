<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'XssSanitizer'], function () {
    Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {
        Route::controller(AuthController::class)->group(function() {
            Route::post('/register', 'register');
            Route::post('/login', 'login');

            Route::post('/forgot-password', 'forgotPassword');
            Route::put('reset-password', 'resetPassword');
            Route::post('resend-verification', 'resendVerificationEmail');
            Route::middleware(['verified', 'jwt.verify'])->group(function () {
                Route::get('/profile', 'profile');
                Route::post('/logout', 'logout');
                Route::post('/change-password', 'changePassword');
            });
        });
        Route::get('/resources', [ResourceController::class, 'resource']);
        Route::get('/jobs', [JobsController::class, 'index']);
        Route::get('/fetch-job-types', [JobsController::class, 'fetchJobTypes']);
        Route::get('/jobs/homepage', [JobsController::class, 'homepage']);
        
        Route::prefix("upload")->group(function() {
            Route::post('/file', [UploadsController::class, 'uploadFile']);
        });



        Route::middleware(['verified', 'jwt.verify', 'auth:api'])->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/users', 'index');
            });
        });
        
    });
});


