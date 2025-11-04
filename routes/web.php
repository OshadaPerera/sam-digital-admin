<?php

use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Route to execute storage link command
Route::get('/run-storage-link', function () {
    if (App::environment('production')) {
        abort(403, 'This action is not allowed in production.');
    }

    try {
        Artisan::call('storage:link');

        return 'Storage link has been created successfully!';
    } catch (\Exception $e) {
        return 'Failed to create storage link: '.$e->getMessage();
    }
});

// Route to clear various caches
Route::get('/clear-cache', function () {
    if (App::environment('production')) {
        abort(403, 'This action is not allowed in production.');
    }

    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('permission:cache-reset');

        return 'All caches have been cleared successfully!';
    } catch (\Exception $e) {
        return 'Failed to clear caches: '.$e->getMessage();
    }
});

Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('can:edit profile settings');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('can:edit profile settings');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('can:edit profile settings');
        Route::get('password', [PasswordController::class, 'edit'])->name('password.edit')->middleware('can:edit password settings');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update')->middleware('can:edit password settings');
        Route::get('appearance', [AppearanceController::class, 'edit'])->name('appearance.edit')->middleware('can:edit appearance settings');
    });

    // User Management Routes
    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view users');
        Route::get('/create', 'create')->name('create')->middleware('can:create users');
        Route::post('/', 'store')->name('store')->middleware('can:create users');
        Route::get('/{user}/edit', 'edit')->name('edit')->middleware('can:edit users');
        Route::put('/{user}', 'update')->name('update')->middleware('can:edit users');
        Route::delete('/{user}', 'destroy')->name('destroy')->middleware('can:delete users');
    });

    // Business Profile Routes
    Route::prefix('business-profile')->name('business-profile.')->controller(BusinessProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view business profile');
        Route::post('/', 'store')->name('store')->middleware('can:create business profile');
        Route::get('/{id}', 'show')->name('show')->middleware('can:view business profile');
        Route::put('/{id}', 'update')->name('update')->middleware('can:update business profile');
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware('can:delete business profile');
    });

    // Review Management Routes
    Route::prefix('reviews')->name('reviews.')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view reviews');
        Route::delete('/{review}', 'destroy')->name('destroy')->middleware('can:delete review');
        Route::patch('/{review}/activate', 'activate')->name('activate')->middleware('can:activate review');
        Route::patch('/{review}/deactivate', 'deactivate')->name('deactivate')->middleware('can:deactivate review');
    });

});

require __DIR__.'/auth.php';
