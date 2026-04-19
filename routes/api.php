<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CostItemController;
use App\Http\Controllers\Api\GardenProjectController;
use App\Http\Controllers\Api\OfferController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('clients', ClientController::class);
    Route::apiResource('garden-projects', GardenProjectController::class);
    Route::apiResource('offers', OfferController::class);
    Route::apiResource('cost-items', CostItemController::class);
});
