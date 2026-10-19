<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\VideoApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\AdApiController;
use App\Http\Controllers\Api\LiveChannelApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| PUBLIC CONTENT APIs (Flutter App)
|--------------------------------------------------------------------------
*/

// Categories
Route::get('/categories', [CategoryApiController::class, 'index']);

// Movies
Route::get('/movies', [VideoApiController::class, 'movies']);
Route::get('/movies/{id}', [VideoApiController::class, 'showMovie'])->whereNumber('id');
Route::get('/movies/category/{categoryId}', [VideoApiController::class, 'byCategory']);

// Videos (alias)
// NOTE: /videos/latest must be registered BEFORE /videos/{id}, otherwise
// "latest" is captured as an {id} and the endpoint always returns 404.
Route::get('/videos', [VideoApiController::class, 'movies']);
Route::get('/videos/latest', [VideoApiController::class, 'latest']);
Route::get('/videos/{id}', [VideoApiController::class, 'showMovie'])->whereNumber('id');

// Sliders
Route::get('/sliders', [SliderApiController::class, 'index']);

// Ads
Route::get('/ads', [AdApiController::class, 'index']);
Route::get('/ads/{position}', [AdApiController::class, 'byPosition']);

// Live Channels
Route::get('/live-channels', [LiveChannelApiController::class, 'index']);
Route::get('/channels', [LiveChannelApiController::class, 'index']);

/*
|--------------------------------------------------------------------------
| AUTH APIs (Sanctum tokens for the mobile / TV app)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
});
