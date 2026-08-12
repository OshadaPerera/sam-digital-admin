<?php

namespace App\Providers;

use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share business profile data with all views
        $businessProfile = Schema::hasTable('business_profile') ? BusinessProfile::first() : null;
        view()->share('businessProfile', $businessProfile);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Register the jsonResponse macro
        Response::macro('jsonResponse', function ($success, $message, $data = [], $statusCode = 200) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });


    }
}
