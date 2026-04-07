<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/me', [UserController::class, 'me']);
//     Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');
// });


Route::middleware(['middleware' => 'api_key'])->group(function () {

    // Public routes (Only need API Key)
    Route::post('login', [AuthController::class, 'login']);

     // Public routes (Only need API Key with JWT)
    Route::post('login-jwt', [AuthController::class, 'loginJWT']);

    // Protected routes (Need API Key AND valid JWT)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');
    });
});
