<?php

// Barebones entry point for Vercel debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Setup /tmp storage (Required for Laravel on Vercel)
$dirs = ['/tmp/storage/framework/views', '/tmp/storage/framework/cache', '/tmp/storage/framework/sessions', '/tmp/storage/logs', '/tmp/bootstrap/cache'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
}

// Set environment for Vercel
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_BASE_PATH=' . dirname(__DIR__));
$_ENV['VERCEL'] = '1';

// Check SQLite
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    @touch($dbPath);
}

// Load Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Run PHP 8.2 Patch Silently (it has its own writability checks)
@include __DIR__ . '/../scripts/php82-patch.php';

// Forward to Laravel
require __DIR__ . '/../public/index.php';
