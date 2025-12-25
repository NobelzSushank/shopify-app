<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', fn() => redirect('/install'));
Route::get('/install', [AuthController::class, 'install']);              // ?shop=yourstore.myshopify.com
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('oauth.callback');       // OAuth redirect URI
Route::get('/app', [AuthController::class, 'embedded'])->name('embedded');                 // Serves frontend index (embedded)

