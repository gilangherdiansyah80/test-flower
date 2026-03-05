<?php
require 'vendor/autoload.php';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('vendor'));
$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($regex as $file) {
    if (strpos($file[0], 'vendor/phpunit/') !== false) continue; // Skip phpunit
    if (strpos($file[0], 'vendor/symfony/polyfill') !== false) continue; // Skip polyfills
    
    $content = file_get_contents($file[0]);
    if (strpos($content, 'implements SessionHandlerInterface') !== false || strpos($content, 'implements \SessionHandlerInterface') !== false) {
        if (strpos($content, '#[\ReturnTypeWillChange]') === false && strpos($content, '#[ReturnTypeWillChange]') === false) {
            echo $file[0] . "\n";
        }
    }
}
