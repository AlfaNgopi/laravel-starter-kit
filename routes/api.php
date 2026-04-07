<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuppliersAPI;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\SupplierController;
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
        Route::post('/logoutjwt', [AuthController::class, 'logoutJWT']);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');


        // CRUD Supplier
        // Suppliers Management - dengan permission check
        Route::get('/suppliers', [SuppliersAPI::class, 'index'])->name('suppliers.index')->middleware('permission:view-suppliers');
        

        Route::post('/suppliers-create', [SuppliersAPI::class, 'store'])->name('suppliers.store')->middleware('permission:create-suppliers');

        Route::get('/suppliers/{supplier}', [SuppliersAPI::class, 'show'])->name('suppliers.show')->middleware('permission:show-suppliers');
        
        Route::put('/suppliers-update/{supplier}', [SuppliersAPI::class, 'update'])->name('suppliers.update')->middleware('permission:edit-suppliers');

        Route::delete('/suppliers-delete/{supplier}', [SuppliersAPI::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:delete-suppliers');
    });
});
