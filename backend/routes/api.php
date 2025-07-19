<?php

use App\Http\Controllers\Api\AuthController;
use AuthController as GlobalAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::get('/users', [UserController::class, 'index']);
// Route::get('/users/{id}', [UserController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // // User Management
    // Route::post('/users', [UserController::class, 'store']);
    // Route::put('/users/{id}', [UserController::class, 'update']);
    // Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // // Category Management
    // Route::post('/categories', [CategoryController::class, 'store']);
    // Route::put('/categories/{id}', [CategoryController::class, 'update']);
    // Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


    // // Order Management
    // Route::get('/orders', [OrderController::class, 'index']);
    // Route::get('/orders/{id}', [OrderController::class, 'show']);
    // Route::post('/orders', [OrderController::class, 'store']);
    // Route::put('/orders/{id}', [OrderController::class, 'update']);
    // Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    // // Flower Management
    // Route::post('/flowers', [FlowerController::class, 'store']);
    // Route::put('/flowers/{id}', [FlowerController::class, 'update']);
    // Route::delete('/flowers/{id}', [FlowerController::class, 'destroy']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});


