<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\VideoApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\AdApiController;
use App\Http\Controllers\Api\LiveChannelApiController;

/*
|--------------------------------------------------------------------------
| PUBLIC CONTENT APIs (Flutter App)
|--------------------------------------------------------------------------
*/

// Categories
Route::get('/categories', [CategoryApiController::class, 'index']);

// Movies
Route::get('/movies', [VideoApiController::class, 'movies']);
Route::get('/movies/{id}', [VideoApiController::class, 'showMovie']);
Route::get('/movies/category/{categoryId}', [VideoApiController::class, 'byCategory']);

// Videos (alias)
Route::get('/videos', [VideoApiController::class, 'movies']);
Route::get('/videos/{id}', [VideoApiController::class, 'showMovie']);
Route::get('/videos/latest', [VideoApiController::class, 'latest']);

// Sliders
Route::get('/sliders', [SliderApiController::class, 'index']);

// Ads
Route::get('/ads', [AdApiController::class, 'index']);
Route::get('/ads/{position}', [AdApiController::class, 'byPosition']);

// Live Channels
Route::get('/live-channels', [LiveChannelApiController::class, 'index']);
Route::get('/channels', [LiveChannelApiController::class, 'index']);