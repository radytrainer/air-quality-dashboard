<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AqiEuropeController;
use App\Http\Controllers\Api\AqiAmericasController;
use App\Http\Controllers\Api\AqiAsiaController;
use App\Http\Controllers\Api\AqiAfricanController;
use App\Http\Controllers\Api\AqiOceaniaController;
use App\Http\Controllers\Api\OpenAQController;
use App\Http\Controllers\Api\WeatherAqiController;

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



Route::get('/air-quality/worldwide', [OpenAQController::class, 'fetchWorldwideData']);

//Europe countries
Route::get('/air-quality/europe-countrie-cities', [AqiEuropeController::class, 'getAllCitiesAirQuality']);
//Americas countries
Route::get('/air-quality/americas-countrie-cities', [AqiAmericasController::class, 'getAlliAmericasCitiesAirQuality']);
//Asia countries
Route::get('/air-quality/Asia-countrie-cities', [AqiAsiaController::class, 'getAllAsiaCitiesAirQuality']);
// African countries
Route::get('/air-quality/African-countrie-cities', [AqiAfricanController::class, 'getAllAfricanCitiesAirQuality']);
//Oceania countries
Route::get('/air-quality/Oceania-countrie-cities', [AqiOceaniaController::class, 'getAllOceaniaCitiesAirQuality']);

Route::get('/aqi-asia', [AqiAsiaController::class, 'getAllAsiaCitiesAirQuality']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // User routes
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    // Profile routes (combined into one group)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/image', [ProfileController::class, 'uploadImage']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']); // kept as is

     Route::get('/air-quality/worldwide', [AirQualityController::class, 'fetchWorldwideData']);
    Route::get('/air-quality/location', [AirQualityController::class, 'fetchByLocation']);
    Route::get('/air-quality/parameters', [AirQualityController::class, 'getParameters']);
    Route::get('/air-quality/countries', [AirQualityController::class, 'getCountries']);
    

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes nested inside auth:sanctum
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    });

});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);            // List all users
    Route::post('/users', [UserController::class, 'store']);           // Create user
    Route::get('/users/{id}', [UserController::class, 'show']);        // View one user
    Route::put('/users/{id}', [UserController::class, 'update']);      // Update user
    Route::delete('/users/{id}', [UserController::class, 'destroy']);  // Delete user

    Route::get('/me', [UserController::class, 'profile']);             // Authenticated user profile
    Route::put('/me', [UserController::class, 'updateProfile']);       // Update own profile
    Route::delete('/me/profile-image', [UserController::class, 'removeProfileImage']); // Remove image
    Route::get('/me/role', [UserController::class, 'role']);           // Get role
});