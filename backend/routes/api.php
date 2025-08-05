<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\AirQualityController;
use App\Http\Controllers\Api\AirQualityDataController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AqiEuropeController;
use App\Http\Controllers\Api\AqiAmericasController;
use App\Http\Controllers\Api\AqiAsiaController;
use App\Http\Controllers\Api\AqiAfricanController;
use App\Http\Controllers\Api\AqiController;
use App\Http\Controllers\Api\AqiOceaniaController;
use App\Http\Controllers\Api\WeatherAqiController;
use App\Http\Controllers\FireDataController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public routes and routes protected by auth:sanctum middleware.
|
*/

// Public routes
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Air quality public data
Route::prefix('air-quality')->group(function () {
    Route::get('/{lat}/{lon}', [AirQualityController::class, 'getAirQuality']);
    Route::get('/phnom-penh', [AirQualityController::class, 'getPhnomPenhAirQuality']);
    Route::get('/locations', [AirQualityController::class, 'getLocations']);
    Route::get('/global', [AirQualityController::class, 'getGlobalAirQuality']);
    Route::get('/latest', [AirQualityController::class, 'getLatestAirQuality']);
    Route::get('/countries', [AirQualityController::class, 'getCountries']);
    Route::get('/counts', [AirQualityController::class, 'getAllCountriesMeasurementCounts']);
    Route::get('/latest/{country}', [AirQualityController::class, 'getLatestByCountry']);
    Route::get('/iqair', [AirQualityController::class, 'getIqAirData']);
    Route::get('/cambodia-all-cities', [AirQualityController::class, 'getAllCitiesAirQuality']);
    Route::get('/pm25', [AirQualityController::class, 'getPm25Concentration']);
});

// Additional AQI endpoints
Route::get('/aqi', [AqiController::class, 'getCityAqi']);
Route::get('/aqi-global', [AqiController::class, 'global']);
Route::get('/airquality', [AqiController::class, 'getGlobalAQI']);

// Routes protected by authentication
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);              // List users
        Route::post('/', [UserController::class, 'store']);             // Create user
        Route::get('/{id}', [UserController::class, 'show']);           // Show user
        Route::put('/{id}', [UserController::class, 'update']);         // Update user
        Route::delete('/{id}', [UserController::class, 'destroy']);     // Delete user
    });

    // Authenticated user's own profile routes
    Route::get('/me', [UserController::class, 'profile']);
    Route::put('/me', [UserController::class, 'updateProfile']);
    Route::delete('/me/profile-image', [UserController::class, 'removeProfileImage']);
    Route::get('/me/role', [UserController::class, 'role']);

    // Profile routes (can consider merging with /me routes if preferred)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/image', [ProfileController::class, 'uploadImage']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']); // Kept as is (possibly duplicate with /me PUT)

    // Admin-only routes inside auth middleware
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
        // Add other admin routes here if any
    });

});
