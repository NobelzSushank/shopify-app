<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('auth/install', [AuthController::class, 'install']);
// Route::get('auth/callback', [AuthController::class, 'callback']);


// Route::middleware('verify.session')->group(function() {
//     Route::get('/stats', [StatsController::class, 'overview']);
//     Route::get('/products', [ProductsController::class, 'index']); // paginated, filters
//     Route::post('/sync/products', [SyncController::class, 'products']); // manual sync trigger
// });

// // Webhooks
// Route::post('/webhooks/products', [WebhookController::class, 'handle'])->middleware('verify.shopify.webhook');

Route::middleware('shop.session')->group(function () {
    Route::get('/summary', [SummaryController::class, 'index']);
    Route::get('/products', [ProductsController::class, 'index']);   // search, filter, paginate
    Route::post('/sync/products', [SyncController::class, 'products']);
});

Route::post('/webhooks/products', [WebhookController::class, 'products']); // Bonus webhooks
