<?php

namespace App\Providers;

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
        try {
            // Share system settings globally
            $settings = \App\Models\SystemSetting::pluck('value', 'key')->toArray();
            \Illuminate\Support\Facades\View::share('settings', $settings);
        } catch (\Exception $e) {
            // Failsafe during migrations or if table doesn't exist
            \Illuminate\Support\Facades\View::share('settings', []);
        }

        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        \Illuminate\Support\Facades\Gate::define('global-admin', function ($user) {
            return $user->isGlobalAdmin();
        });
    }
}
