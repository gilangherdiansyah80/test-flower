<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ErrorSuppressProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     * 
     * Re-set error_reporting AFTER Laravel's HandleExceptions
     * has set it to -1 (E_ALL). This prevents E_DEPRECATED
     * from being converted to thrown ErrorExceptions.
     */
    public function boot()
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    }

    public function register()
    {
        //
    }
}
