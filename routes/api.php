<?php

use App\Http\Controllers\Api\V1\SubscriptionController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(SubscriptionController::class)->prefix('v1/')->group(function () {
    Route::get('subscription/current-user-subscription', 'userCurrentSubscription');
    Route::apiResource("subscription", SubscriptionController::class)->only("index", "store", "update");
});
