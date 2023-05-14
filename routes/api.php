<?php

use App\Http\Controllers\Api\V1\PaymentGatewayController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SubscriptionController;
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

Route::prefix('/v1')->group(function () {
    Route::apiResource('payment-gateways', PaymentGatewayController::class)->only('index', 'show', 'store', 'update');
    Route::apiResource('products', ProductController::class)->only('index', 'store', 'update');
    Route::apiResource('plans', PlanController::class)->only('index', 'show', 'store');
    Route::patch('plans/{plan}/activate', [PlanController::class, 'activate']);
    Route::patch('plans/{plan}/deactivate', [PlanController::class, 'deactivate']);
    Route::apiResource('subscriptions', SubscriptionController::class)->only('index', 'store', 'update');
});

