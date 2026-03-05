<?php

/**
 * Laravel on Vercel
 * Serverless entry point - patches vendor files and redirects writable paths to /tmp
 */

// Run PHP 8.2 compatibility patches (only once per cold start)
$patchLock = '/tmp/.patches_applied';
if (!file_exists($patchLock)) {
    require_once __DIR__ . '/../scripts/php82-patch.php';
    file_put_contents($patchLock, date('Y-m-d H:i:s'));
}

// Suppress any remaining PHP 8.2+ deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

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
