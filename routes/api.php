<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('what-we-do', App\Http\Controllers\API\WhatWeDoController::class)->only(['index', 'show']);
Route::apiResource('gallery', App\Http\Controllers\API\GalleryController::class)->only(['index', 'show']);
Route::apiResource('reviews', App\Http\Controllers\API\ReviewController::class)->only(['index', 'store']);