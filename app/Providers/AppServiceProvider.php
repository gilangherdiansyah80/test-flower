<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') !== 'local') {
            \URL::forceScheme('https');
        }

        // AUTO-MIGRATE (Railway resilience)
        try {
            if (!\Schema::hasTable('favorites')) {
                \Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Exception $e) {
            // Silently fail to avoid crashing boot
        }
    }
}
