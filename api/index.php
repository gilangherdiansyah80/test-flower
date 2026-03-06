<?php

/**
 * Laravel on Vercel
 * Serverless entry point
 */

// Custom error handler that silently suppresses Deprecated/Notice warnings
// This MUST come before anything else, and does NOT use output buffering
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if ($errno === E_DEPRECATED || $errno === E_NOTICE || $errno === E_USER_DEPRECATED || $errno === E_USER_NOTICE) {
        return true;
    }
    return false;
});

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// Enable error display for debugging on Vercel
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Ensure /tmp/storage directories exist
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Set required environment variables for writable paths
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

try {
    // Check if vendor folder exists
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        throw new Exception("Vendor autoload not found. Composer install might have failed.");
    }

    // Check if patches are applied (check Container.php)
    $containerRef = new ReflectionClass('Illuminate\Container\Container');
    $method = $containerRef->getMethod('offsetExists');
    $isPatched = strpos(file_get_contents($containerRef->getFileName()), 'ReturnTypeWillChange') !== false;

    if (!$isPatched) {
        // Attempt to run patch script if not patched (though filesystem might be read-only here)
        @include __DIR__ . '/../scripts/php82-patch.php';
    }

    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    echo "<h1>Deployment Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

