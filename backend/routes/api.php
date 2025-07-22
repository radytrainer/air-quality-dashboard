<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use AuthController as GlobalAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AirQualityController;
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

Route::get('/air-quality/{lat}/{lon}', [AirQualityController::class, 'getAirQuality']);
Route::get('/air-quality/phnom-penh', [AirQualityController::class, 'getPhnomPenhAirQuality']);
Route::get('/countries', [AirQualityController::class, 'getCountries']);
Route::get('/latest/{code}', [AirQualityController::class, 'getLatestByCountry']);
Route::get('/air-quality/locations', [AirQualityController::class, 'getLocation']);
Route::get('/air-quality/global', [AirQualityController::class, 'getGlobalAirQuality']);
Route::get('/air-quality/counts', [AirQualityController::class, 'getAllCountriesMeasurementCounts']);

Route::get('/iqair-air-quality', [AirQualityController::class, 'getIqAirData']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    //For admin role
    
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});


