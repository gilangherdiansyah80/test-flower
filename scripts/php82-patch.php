<?php

/**
 * PHP 8.2 Compatibility Patch Script for Laravel 5.8
 * 
 * This script automatically patches vendor files after composer install
 * to fix PHP 8.2+ deprecations and fatal errors in Laravel 5.8.
 * 
 * Run via: php scripts/php82-patch.php
 */

$basePath = dirname(__DIR__) . '/vendor/laravel/framework/src/Illuminate';
$patchCount = 0;
$isVercel = isset($_ENV['VERCEL']) || getenv('VERCEL');

// Helper function to patch a file
function patchFile($filePath, $search, $replace) {
    global $patchCount, $isVercel;
    if (!file_exists($filePath)) {
        return false;
    }
    
    // On Vercel at runtime, the filesystem is read-only. 
    // We skip patching if we can't write, to avoid warnings/500 errors.
    if (!is_writable($filePath)) {
        return false;
    }

    $content = file_get_contents($filePath);
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        file_put_contents($filePath, $content);
        $patchCount++;
        return true;
    }
    return false;
}

// Silenced for web requests to prevent header issues


// ============================================================
// 1. Container.php - Add #[\ReturnTypeWillChange] to ArrayAccess methods
// ============================================================
$containerFile = $basePath . '/Container/Container.php';
if (file_exists($containerFile)) {
    $content = file_get_contents($containerFile);
    
    // Make idempotent by removing existing attributes
    $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
    
    // offsetExists
    $content = preg_replace(
        '/(\s+)public function offsetExists\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetExists($key)',
        $content
    );
    // offsetGet
    $content = preg_replace(
        '/(\s+)public function offsetGet\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetGet($key)',
        $content
    );
    // offsetSet
    $content = preg_replace(
        '/(\s+)public function offsetSet\(\$key, \$value\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetSet($key, $value)',
        $content
    );
    // offsetUnset
    $content = preg_replace(
        '/(\s+)public function offsetUnset\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetUnset($key)',
        $content
    );
    
    // Remove duplicate attributes if already patched
    $content = str_replace("#[\\ReturnTypeWillChange]\n    #[\\ReturnTypeWillChange]", "#[\\ReturnTypeWillChange]", $content);
    
    // Fix getClass() -> getType() in resolveClass method ($parameter and $dependency)
    $content = preg_replace(
        '/\$(\w+)->getClass\(\)/',
        '(\$$1->getType() && !\$$1->getType()->isBuiltin() ? new \ReflectionClass(\$$1->getType()->getName()) : null)',
        $content
    );
    
    file_put_contents($containerFile, $content);
    $patchCount++;

}

// ============================================================
// 2. BoundMethod.php - Fix getClass() deprecation
// ============================================================
$boundMethodFile = $basePath . '/Container/BoundMethod.php';
if (file_exists($boundMethodFile)) {
    $content = file_get_contents($boundMethodFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($boundMethodFile, $content);
    $patchCount++;

}

// ============================================================
// 3. Str.php - Fix mb_* functions null deprecation
// ============================================================
$strFile = $basePath . '/Support/Str.php';
if (file_exists($strFile)) {
    $content = file_get_contents($strFile);
    
    // contains - cast haystack to string
    $content = str_replace(
        "return mb_strpos(\$haystack, \$needle) !== false;",
        "return mb_strpos((string)\$haystack, (string)\$needle) !== false;",
        $content
    );
    
    // length
    $content = str_replace(
        "return mb_strlen(\$value);",
        "return mb_strlen((string)\$value);",
        $content
    );
    $content = str_replace(
        "return mb_strlen(\$value, \$encoding);",
        "return mb_strlen((string)\$value, \$encoding);",
        $content
    );
    
    // lower
    $content = str_replace(
        "return mb_strtolower(\$value, 'UTF-8');",
        "return mb_strtolower((string)\$value, 'UTF-8');",
        $content
    );
    
    // upper
    $content = str_replace(
        "return mb_strtoupper(\$value, 'UTF-8');",
        "return mb_strtoupper((string)\$value, 'UTF-8');",
        $content
    );
    
    // title
    $content = str_replace(
        "return mb_convert_case(\$value, MB_CASE_TITLE, 'UTF-8');",
        "return mb_convert_case((string)\$value, MB_CASE_TITLE, 'UTF-8');",
        $content
    );
    
    // substr
    $content = str_replace(
        "return mb_substr(\$string, \$start, \$length, 'UTF-8');",
        "return mb_substr((string)\$string, \$start, \$length, 'UTF-8');",
        $content
    );
    
    // limit - mb_strwidth and mb_strimwidth
    $content = str_replace(
        "mb_strwidth(\$value, 'UTF-8')",
        "mb_strwidth((string)\$value, 'UTF-8')",
        $content
    );
    $content = str_replace(
        "mb_strimwidth(\$value,",
        "mb_strimwidth((string)\$value,",
        $content
    );
    
    file_put_contents($strFile, $content);
    $patchCount++;

}

// ============================================================
// 4. helpers.php - Fix e() function null deprecation
// ============================================================
$helpersFile = $basePath . '/Support/helpers.php';
patchFile($helpersFile,
    "return htmlspecialchars(\$value, ENT_QUOTES, 'UTF-8', \$doubleEncode);",
    "return htmlspecialchars((string)\$value, ENT_QUOTES, 'UTF-8', \$doubleEncode);"
);

// ============================================================
// 5. Request.php - Fix prefetch/strcasecmp null deprecation
// ============================================================
$requestFile = $basePath . '/Http/Request.php';
if (file_exists($requestFile)) {
    $content = file_get_contents($requestFile);
    
    // Make idempotent
    $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
    
    // Add #[\ReturnTypeWillChange] to offsetExists, offsetGet, offsetSet, offsetUnset
    $content = preg_replace(
        '/(\s+)public function offsetExists\(\$offset\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetExists($offset)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetGet\(\$offset\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetGet($offset)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetSet\(\$offset, \$value\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetSet($offset, $value)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetUnset\(\$offset\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetUnset($offset)',
        $content
    );
    
    // Remove duplicate attributes
    $content = str_replace("#[\\ReturnTypeWillChange]\n    #[\\ReturnTypeWillChange]", "#[\\ReturnTypeWillChange]", $content);
    
    file_put_contents($requestFile, $content);
    $patchCount++;

}

// ============================================================
// 6. MessageBag.php - Add #[\ReturnTypeWillChange]
// ============================================================
$messageBagFile = $basePath . '/Support/MessageBag.php';
if (file_exists($messageBagFile)) {
    $content = file_get_contents($messageBagFile);
    
    // Make idempotent
    $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
    
    $content = preg_replace(
        '/(\s+)public function count\(\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function count()',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function jsonSerialize\(\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function jsonSerialize()',
        $content
    );
    
    file_put_contents($messageBagFile, $content);
    $patchCount++;

}

// ============================================================
// 7. RouteCollection.php - Add #[\ReturnTypeWillChange]
// ============================================================
$routeCollectionFile = $basePath . '/Routing/RouteCollection.php';
if (file_exists($routeCollectionFile)) {
    $content = file_get_contents($routeCollectionFile);
    
    // Make idempotent
    $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
    
    $content = preg_replace(
        '/(\s+)public function getIterator\(\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function getIterator()',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function count\(\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function count()',
        $content
    );
    
    file_put_contents($routeCollectionFile, $content);
    $patchCount++;

}

// ============================================================
// 8. RouteSignatureParameters.php - Fix getClass() deprecation
// ============================================================
$routeSigFile = $basePath . '/Routing/RouteSignatureParameters.php';
if (file_exists($routeSigFile)) {
    $content = file_get_contents($routeSigFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($routeSigFile, $content);
    $patchCount++;

}

// ============================================================
// 9. RouteDependencyResolverTrait.php - Fix getClass() deprecation
// ============================================================
$routeDepFile = $basePath . '/Routing/RouteDependencyResolverTrait.php';
if (file_exists($routeDepFile)) {
    $content = file_get_contents($routeDepFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($routeDepFile, $content);
    $patchCount++;

}

// ============================================================
// 10. ImplicitRouteBinding.php - Fix getClass() deprecation
// ============================================================
$implicitFile = $basePath . '/Routing/ImplicitRouteBinding.php';
if (file_exists($implicitFile)) {
    $content = file_get_contents($implicitFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($implicitFile, $content);
    $patchCount++;

}

// ============================================================
// 11. DiscoverEvents.php - Fix getClass() deprecation
// ============================================================
$discoverFile = $basePath . '/Foundation/Events/DiscoverEvents.php';
if (file_exists($discoverFile)) {
    $content = file_get_contents($discoverFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($discoverFile, $content);
    $patchCount++;

}

// ============================================================
// 12. Broadcaster.php - Fix getClass() deprecation
// ============================================================
$broadcasterFile = $basePath . '/Broadcasting/Broadcasters/Broadcaster.php';
if (file_exists($broadcasterFile)) {
    $content = file_get_contents($broadcasterFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($broadcasterFile, $content);
    $patchCount++;

}

// ============================================================
// 13. Gate.php - Fix getClass() deprecation
// ============================================================
$gateFile = $basePath . '/Auth/Access/Gate.php';
if (file_exists($gateFile)) {
    $content = file_get_contents($gateFile);
    $content = str_replace(
        '$parameter->getClass()',
        '($parameter->getType() && !$parameter->getType()->isBuiltin() ? new \ReflectionClass($parameter->getType()->getName()) : null)',
        $content
    );
    file_put_contents($gateFile, $content);
    $patchCount++;

}

// ============================================================
// 14. Config/Repository.php - Add #[\ReturnTypeWillChange]
// ============================================================
$configRepoFile = $basePath . '/Config/Repository.php';
if (file_exists($configRepoFile)) {
    $content = file_get_contents($configRepoFile);
    
    // Make idempotent
    $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
    
    $content = preg_replace(
        '/(\s+)public function offsetExists\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetExists($key)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetGet\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetGet($key)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetSet\(\$key, \$value\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetSet($key, $value)',
        $content
    );
    $content = preg_replace(
        '/(\s+)public function offsetUnset\(\$key\)/',
        '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function offsetUnset($key)',
        $content
    );
    
    file_put_contents($configRepoFile, $content);
    $patchCount++;

}

// ============================================================
// 15. Session Handlers - Add #[\ReturnTypeWillChange]
// ============================================================
$sessionFiles = glob($basePath . '/Session/*SessionHandler.php');
if ($sessionFiles === false) $sessionFiles = [];
// Also include CacheBasedSessionHandler and similar
$sessionFiles = array_merge($sessionFiles, glob($basePath . '/Session/*Handler.php') ?: []);
$sessionFiles = array_unique($sessionFiles);

foreach ($sessionFiles as $sessionFile) {
    if (file_exists($sessionFile)) {
        $content = file_get_contents($sessionFile);
        
        // Make idempotent
        $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
        $modified = false;
        
        foreach (['open', 'close', 'read', 'write', 'destroy', 'gc'] as $method) {
            if (preg_match('/public function ' . $method . '\(/', $content)) {
                $content = preg_replace(
                    '/(\s+)public function ' . $method . '\(/',
                    '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function ' . $method . '(',
                    $content
                );
                $modified = true;
            }
        }
        
        if ($modified) {
            $content = str_replace("#[\\ReturnTypeWillChange]\n    #[\\ReturnTypeWillChange]", "#[\\ReturnTypeWillChange]", $content);
            file_put_contents($sessionFile, $content);
            $patchCount++;

        }
    }
}

// ============================================================
// 16. Opis Closure - Fix Serializable deprecation  
// ============================================================
$opisFile = dirname(__DIR__) . '/vendor/opis/closure/src/SerializableClosure.php';
if (file_exists($opisFile)) {
    $content = file_get_contents($opisFile);
    if (strpos($content, '#[\ReturnTypeWillChange]') === false) {
        $content = preg_replace(
            '/(\s+)public function serialize\(\)/',
            '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function serialize()',
            $content
        );
        $content = preg_replace(
            '/(\s+)public function unserialize\(\$data\)/',
            '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function unserialize($data)',
            $content
        );
        file_put_contents($opisFile, $content);
        $patchCount++;

    }
}

// ============================================================
// 17. Support Classes - Add #[\ReturnTypeWillChange] to ArrayAccess
// ============================================================
$supportFiles = [
    $basePath . '/Support/Collection.php',
    $basePath . '/Support/Fluent.php',
    $basePath . '/Support/Optional.php',
];

foreach ($supportFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Make idempotent
        $content = preg_replace('/#\[\\\\ReturnTypeWillChange\]\s+/', '', $content);
        $modified = false;
        
        $methods = ['offsetExists', 'offsetGet', 'offsetSet', 'offsetUnset'];
        foreach ($methods as $method) {
            if (preg_match('/(\s+)public function ' . $method . '\(/', $content)) {
                $content = preg_replace(
                    '/(\s+)public function ' . $method . '\(/',
                    '$1#[\ReturnTypeWillChange]' . "\n" . '$1public function ' . $method . '(',
                    $content
                );
                $modified = true;
            }
        }
        
        if ($modified) {
            file_put_contents($file, $content);
            $patchCount++;

        }
    }
}

// ============================================================
// 18. HandleExceptions.php - Prevent E_DEPRECATED from crashing Laravel
// ============================================================
$handleExceptionsFile = $basePath . '/Foundation/Bootstrap/HandleExceptions.php';
if (file_exists($handleExceptionsFile)) {
    $content = file_get_contents($handleExceptionsFile);
    if (strpos($content, 'E_DEPRECATED || $level === E_USER_DEPRECATED') === false) {
        $content = preg_replace(
            '/(\s+)public function handleError\(\$level, \$message, \$file = \'\', \$line = 0, \$context = \[\]\)\n\s+\{/',
            "$0\n        if (\$level === E_DEPRECATED || \$level === E_USER_DEPRECATED || \$level === E_NOTICE || \$level === E_USER_NOTICE) { return; }\n",
            $content
        );
        file_put_contents($handleExceptionsFile, $content);
        $patchCount++;

    }
}


