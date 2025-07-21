<?php

use App\Http\Controllers\Api\AdminController;
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

    //For admin role
    
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin-only', [AdminController::class, 'dashboard']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});


