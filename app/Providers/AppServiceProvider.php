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
        // Force database configuration at runtime to bypass any Railway/Env overrides or cache
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => database_path('database.sqlite'),
        ]);

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
