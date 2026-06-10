<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\WeatherController;
use App\Http\Controllers\Api\V1\GeocodingController;
use App\Http\Controllers\Api\V1\FavoriteLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth — public
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::prefix('weather')->group(function () {
            Route::get('/current',  [WeatherController::class, 'current']);
            Route::get('/forecast', [WeatherController::class, 'forecast']);
        });

        Route::prefix('geocoding')->group(function () {
            Route::get('/search',   [GeocodingController::class, 'search']);
            Route::get('/boundary', [GeocodingController::class, 'boundary']);
        });

        Route::apiResource('favorites', FavoriteLocationController::class)
            ->only(['index', 'store', 'destroy'])
            ->parameters(['favorites' => 'id']);
    });
});
