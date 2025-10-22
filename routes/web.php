<?php

use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

// Route to execute storage link command
Route::get('/run-storage-link', function () {
    if (App::environment('production')) {
        abort(403, 'This action is not allowed in production.');
    }

    try {
        Artisan::call('storage:link');
        return 'Storage link has been created successfully!';
    } catch (\Exception $e) {
        return 'Failed to create storage link: ' . $e->getMessage();
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
        return 'Failed to clear caches: ' . $e->getMessage();
    }
});

Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');
});

require __DIR__.'/auth.php';
