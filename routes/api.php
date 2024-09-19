<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResumeController;
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
            Route::get('/login', function() {
                return errorResponse("Unauthenticated", [], 321);
            })->name("login");

            Route::post('/forgot-password', 'forgotPassword');
            Route::put('reset-password', 'resetPassword');
            Route::post('resend-verification', 'resendVerificationEmail');
            Route::middleware(['verified', 'jwt.verify'])->group(function () {
                Route::post('/logout', 'logout');
                Route::post('/change-password', 'changePassword');
            });
        });
        Route::get('/resources', [ResourceController::class, 'resource']);
        Route::get('/jobs', [JobsController::class, 'index']);
        Route::middleware(['verified', 'jwt.verify'])->group(function () {
            Route::post('/jobs/apply', [JobsController::class, 'apply']);
        });
        Route::get('/jobs-alert', [JobsController::class, 'alert']);
        Route::get('/jobs/homepage', [JobsController::class, 'homepage']);
        Route::get('/jobs/saved', [JobsController::class, 'saved']);
        Route::post('/jobs/saved/{id}', [JobsController::class, 'savedPost']);
        Route::post('/jobs/saved/delete/{id}', [JobsController::class, 'deletesaved']);
        Route::get('/jobs/{slug}', [JobsController::class, 'show']);
        Route::get('/job/share/{id}', [JobsController::class, 'shareJob']);
        
        Route::get('/jobs/similar/{slug}', [JobsController::class, 'similarJobs']);
        Route::get('/fetch-job-types', [JobsController::class, 'fetchJobTypes']);
        
        Route::prefix("upload")->group(function() {
            Route::post('/file', [UploadsController::class, 'uploadFile']);
        });
        Route::prefix("countries")->group(function() {
            Route::get('/', [CountryController::class, 'index']);
            Route::get('/{code}', [CountryController::class, 'show']);
        });



        Route::middleware(['verified', 'jwt.verify', 'auth:api'])->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/users', 'index');
                Route::post('/user/change-password', 'changePassword');
                Route::post('/user/delete', 'deleteAccount');
                
                Route::get('/profile', 'show');
                Route::post('/profile', 'update');
                Route::get('/profile/delete-avatar', 'deleteAvatar');
                Route::post('/profile/social/{id}', 'updateSocial');
                Route::post('/profile/social-delete/{id}', 'deleteSocial');
                Route::post('/profile/social-add', 'addSocial');
                
                
            });
            Route::prefix("/resume")->controller(ResumeController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('update', 'update');
                Route::post('remove-resume', 'removeResume');
                Route::post('update/intro', 'updateIntro');
                Route::get('delete-portolio/{id}', 'deletePortfolio');
                
            });

            Route::prefix("/chat")->controller(ChatController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('send-chat', 'store');
            });
        });
        
    });
});


