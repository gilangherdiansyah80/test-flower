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
    // Check and require vendor autoloader
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception("Vendor autoload not found at: " . $autoloadPath . ". Current directory: " . getcwd());
    }
    require $autoloadPath;

    // Check if patches are applied (check Container.php via reflection to find its path)
    if (class_exists('Illuminate\Container\Container')) {
        $containerRef = new ReflectionClass('Illuminate\Container\Container');
        $containerFilePath = $containerRef->getFileName();
        $isPatched = strpos(file_get_contents($containerFilePath), 'ReturnTypeWillChange') !== false;

        if (!$isPatched) {
            // Attempt to run patch script if not patched (might be read-only, but worth a try)
            // or we might need to find another way if Vercel build environment doesn't allow this.
            @include __DIR__ . '/../scripts/php82-patch.php';
        }
    }

    // Forward to Laravel's actual entry point (which also requires the autoloader, but that's fine)
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {

    echo "<h1>Deployment Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

