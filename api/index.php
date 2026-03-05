<?php

/**
 * Laravel on Vercel - Diagnostic Version
 * Shows actual errors to help debug deployment issues
 */

// Show ALL errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Check if patch script can run (filesystem might be read-only)
$patchScript = __DIR__ . '/../scripts/php82-patch.php';
if (file_exists($patchScript)) {
    // Try to patch, but wrap in try-catch in case filesystem is read-only
    try {
        ob_start();
        require_once $patchScript;
        $patchOutput = ob_get_clean();
        // Save patch output for debugging
        @file_put_contents('/tmp/patch_output.txt', $patchOutput);
    } catch (\Throwable $e) {
        // If patching fails (read-only filesystem), log it
        @file_put_contents('/tmp/patch_error.txt', $e->getMessage());
    }
}

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
