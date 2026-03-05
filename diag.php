<?php
require 'vendor/autoload.php';
try {
    echo "PHP Version: " . PHP_VERSION . "\n";
    $ref = new ReflectionMethod('Illuminate\Container\Container', 'resolveDependencies');
    echo "File: " . $ref->getFileName() . "\n";
    echo "Start Line: " . $ref->getStartLine() . "\n";
    $rp = new ReflectionClass('ReflectionParameter');
    echo "getClass() exists in ReflectionParameter? " . ($rp->hasMethod('getClass') ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
