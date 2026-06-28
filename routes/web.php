<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;

use App\Models\Category;
use App\Models\Video;
use App\Models\LiveChannel;
use App\Models\Slider;
use App\Models\Ad;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\LiveChannelController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\AdController;

Route::get('/', function () {
    return redirect('/welcome.html');
});



/**
 * ADMIN ROUTES (static left sidebar layout)
 * URL prefix: /admin/...
 * Names: admin.*
 */
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    /**
     * ✅ ADMIN DASHBOARD
     * View: resources/views/admin/dashboard.blade.php
     */
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    /**
     * Admin CRUD
     * These generate:
     * admin.categories.*
     * admin.videos.*
     * admin.live-channels.*
     * admin.sliders.*
     * admin.ads.*
     */
    Route::resource('categories', CategoryController::class);
    Route::resource('videos', VideoController::class);
    Route::resource('live-channels', LiveChannelController::class);
    Route::resource('sliders', SliderController::class);
    Route::resource('ads', AdController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

    // Video processing routes
    Route::post('videos/{video}/convert-to-hls', [VideoController::class, 'convertToHls'])->name('videos.convert-to-hls');
    Route::get('videos/{video}/processing-status', [VideoController::class, 'processingStatus'])->name('videos.processing-status');

    // Chunked upload routes for large files
    Route::post('videos/chunk-upload', [VideoController::class, 'chunkUpload'])->name('videos.chunk-upload');
    Route::post('videos/merge-chunks', [VideoController::class, 'mergeChunks'])->name('videos.merge-chunks');
});


/**
 * PROFILE ROUTES
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
