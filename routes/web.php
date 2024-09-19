<?php

use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
Route::get("/", function( ) {
    $data = [
        'title' => 'ACCOUNT VERIFICATION SUCCESSFUL',
        'to' => "horphy1@gmail.com",
        'full_name' => "Opeyemi",
        'body' => '
        <p>Congratulations! Your account has been successfully verified. You can now enjoy the full benefits of our ' . env('APP_NAME') . '.</p>

        <p>If you have any questions or need assistance, feel free to contact our support team.</p>
        
        <p>Thank you for choosing our service!</p>
       ',
        'hasButton' => true,
        'buttonLink' => env('FRONTEND') . '/login',
        'buttonText' => 'My Account',
    ];
        return view("emails.template", ["data" => $data]);
});