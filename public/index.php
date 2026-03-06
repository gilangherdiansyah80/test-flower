<?php

// SILENCE THE NOISE - Crucial for Laravel 5.8 on PHP 8.2 (Railway/Vercel)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_DEPRECATED & ~E_USER_NOTICE);
ini_set('display_errors', '0');

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $ignored = [E_DEPRECATED, E_USER_DEPRECATED, E_NOTICE, E_USER_NOTICE];
    if (in_array($errno, $ignored)) return true;
    return false;
});

// Apply patches if they exist
if (file_exists(__DIR__.'/../scripts/php82-patch.php')) {
    @include __DIR__.'/../scripts/php82-patch.php';
}


/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

// AUTO-PREPARE DATABASE (Railway resilience)
$dbPath = __DIR__.'/../database/database.sqlite';
if (!file_exists($dbPath)) {
    @file_put_contents($dbPath, '');
}

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
