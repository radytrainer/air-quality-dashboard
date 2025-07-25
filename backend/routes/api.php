<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\AirQualityController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
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
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/air-quality/{lat}/{lon}', [AirQualityController::class, 'getAirQuality']);
Route::get('/air-quality/phnom-penh', [AirQualityController::class, 'getPhnomPenhAirQuality']);
Route::get('/countries', [AirQualityController::class, 'getCountries']);
Route::get('/latest/{code}', [AirQualityController::class, 'getLatestByCountry']);
Route::get('/openaq/locations/{id}', [AirQualityController::class, 'getLocation']);

Route::get('/users', [UserController::class, 'index']);

// Admin contacts (no middleware applied here)
Route::prefix('admin')->group(function () {
    Route::get('/contacts', [AdminContactController::class, 'index']);
    Route::get('/contacts/{id}', [AdminContactController::class, 'show']);
});

// Routes requiring authentication
Route::middleware('auth:sanctum')->group(function () {

    // User routes
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    // Profile routes (combined into one group)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/image', [ProfileController::class, 'uploadImage']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']); // kept as is

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes nested inside auth:sanctum
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    });

});
