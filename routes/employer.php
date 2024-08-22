<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['verified', 'jwt.verify', 'auth:api', 'employer'])->group(function () {
    
});
