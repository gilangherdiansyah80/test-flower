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

// Ensure /tmp/storage directories exist for Laravel's writable needs
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Forward requests to Laravel
require __DIR__ . '/../public/index.php';
