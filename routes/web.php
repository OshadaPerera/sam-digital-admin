<?php

use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WhatWeDoController;
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

Route::get('/', [DashboardController::class, 'index'])
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

    // Role Management Routes
    Route::prefix('roles')->name('roles.')->controller(RoleController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view roles');
        Route::get('/create', 'create')->name('create')->middleware('can:create role');
        Route::post('/', 'store')->name('store')->middleware('can:create role');
        Route::get('/{role}/edit', 'edit')->name('edit')->middleware('can:edit role');
        Route::put('/{role}', 'update')->name('update')->middleware('can:edit role');
        Route::delete('/{role}', 'destroy')->name('destroy')->middleware('can:delete role');
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

    // What We Do Management Routes
    Route::prefix('what-we-do')->name('what-we-do.')->controller(WhatWeDoController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view what we do');
        Route::get('/create', 'create')->name('create')->middleware('can:create what we do');
        Route::post('/', 'store')->name('store')->middleware('can:create what we do');
        Route::get('/{whatWeDo}', 'show')->name('show')->middleware('can:view what we do');
        Route::get('/{whatWeDo}/edit', 'edit')->name('edit')->middleware('can:edit what we do');
        Route::put('/{whatWeDo}', 'update')->name('update')->middleware('can:edit what we do');
        Route::delete('/{whatWeDo}', 'destroy')->name('destroy')->middleware('can:delete what we do');
        Route::patch('/{whatWeDo}/activate', 'activate')->name('activate')->middleware('can:activate what we do');
        Route::patch('/{whatWeDo}/deactivate', 'deactivate')->name('deactivate')->middleware('can:deactivate what we do');
        Route::post('/{whatWeDo}/images', 'addImages')->name('add-images')->middleware('can:add what we do images');
        Route::delete('/{whatWeDo}/images/{image}', 'deleteImage')->name('delete-image')->middleware('can:delete what we do images');
    });

    // Gallery Management Routes
    Route::prefix('gallery')->name('gallery.')->controller(GalleryController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view gallery');
        Route::get('/create', 'create')->name('create')->middleware('can:create gallery');
        Route::post('/', 'store')->name('store')->middleware('can:create gallery');
        Route::get('/{gallery}', 'show')->name('show')->middleware('can:view gallery');
        Route::get('/{gallery}/edit', 'edit')->name('edit')->middleware('can:edit gallery');
        Route::put('/{gallery}', 'update')->name('update')->middleware('can:edit gallery');
        Route::delete('/{gallery}', 'destroy')->name('destroy')->middleware('can:delete gallery');
        Route::patch('/{gallery}/activate', 'activate')->name('activate')->middleware('can:activate gallery');
        Route::patch('/{gallery}/deactivate', 'deactivate')->name('deactivate')->middleware('can:deactivate gallery');
        Route::post('/{gallery}/images', 'addImages')->name('add-images')->middleware('can:add gallery images');
        Route::delete('/{gallery}/images/{image}', 'deleteImage')->name('delete-image')->middleware('can:delete gallery images');
    });

    // Video Management Routes
    Route::prefix('videos')->name('videos.')->controller(VideoController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('can:view videos');
        Route::get('/create', 'create')->name('create')->middleware('can:create videos');
        Route::post('/', 'store')->name('store')->middleware('can:create videos');
        Route::get('/{video}/edit', 'edit')->name('edit')->middleware('can:edit videos');
        Route::put('/{video}', 'update')->name('update')->middleware('can:edit videos');
        Route::delete('/{video}', 'destroy')->name('destroy')->middleware('can:delete videos');
        Route::patch('/{video}/activate', 'activate')->name('activate')->middleware('can:activate videos');
        Route::patch('/{video}/deactivate', 'deactivate')->name('deactivate')->middleware('can:deactivate videos');
    });

});

require __DIR__.'/auth.php';
