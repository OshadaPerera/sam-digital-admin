<?php

use Illuminate\Support\Facades\Route;

// Public API routes - no authentication required
Route::withoutMiddleware(['auth:sanctum'])->group(function () {
    Route::apiResource('what-we-do', App\Http\Controllers\API\WhatWeDoController::class)->only(['index', 'show'])->names('api.what-we-do');
    Route::apiResource('gallery', App\Http\Controllers\API\GalleryController::class)->only(['index', 'show'])->names('api.gallery');
    Route::apiResource('reviews', App\Http\Controllers\API\ReviewController::class)->only(['index', 'store'])->names('api.reviews');
    Route::apiResource('videos', App\Http\Controllers\API\VideoController::class)->only(['index', 'show'])->names('api.videos');
});