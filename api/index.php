<?php

/**
 * Laravel on Vercel
 * Optimized Entry Point for PHP 8.2 Compatibility
 */

// 1. SILENCE THE NOISE - Crucial for Laravel 5.8 on PHP 8.2
// We intercept all Deprecated and Notice level errors before they break anything.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_DEPRECATED & ~E_USER_NOTICE);
ini_set('display_errors', '0');

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // List of error levels we want to ignore completely
    $ignored = [E_DEPRECATED, E_USER_DEPRECATED, E_NOTICE, E_USER_NOTICE];
    if (in_array($errno, $ignored)) {
        return true; // Stop error from propagating
    }
    return false; // Let fatal errors through
});

// 2. STORAGE SETUP - Vercel requires write access to /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 3. ENVIRONMENT CONFIG
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');
$_ENV['VERCEL'] = '1';

// 4. BOOTSTRAP
// Load vendor autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

// Attempt to apply patches (Silent by default)
@include __DIR__ . '/../scripts/php82-patch.php';

// Check for SQLite database
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    @touch($dbPath);
}

// 5. RUN LARAVEL
require __DIR__ . '/../public/index.php';
