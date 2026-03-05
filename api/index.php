<?php

/**
 * Laravel on Vercel
 * Serverless entry point
 */

// Install a custom error handler that silences Deprecated and Notice warnings
// This is more reliable than error_reporting() because Laravel can't override it
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // Suppress Deprecated and Notice warnings entirely
    if ($errno === E_DEPRECATED || $errno === E_NOTICE || $errno === E_USER_DEPRECATED) {
        return true; // Handled (suppressed)
    }
    // Let other errors pass through to the default handler
    return false;
});

// Also set error_reporting as a safety net
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

// Start output buffering to catch any warnings that slip through
ob_start();

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

// Forward requests to the original Laravel public/index.php
require __DIR__ . '/../public/index.php';

// Get buffered output and remove any Deprecated warning lines that may have slipped through
$output = ob_get_clean();
$output = preg_replace('/^Deprecated:.*$/m', '', $output);
$output = preg_replace('/^Warning:.*$/m', '', $output);
$output = preg_replace('/^\s*\n/m', '', $output); // Remove empty lines left over
echo $output;
