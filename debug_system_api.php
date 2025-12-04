<?php

require __DIR__ . '/vendor/autoload.php';

$openapi = json_decode(file_get_contents('openapi.json'), true);

$paths = [];
foreach ($openapi['paths'] as $path => $methods) {
    if (strpos($path, '/system') !== false) {
        $paths[$path] = array_keys($methods);
    }
}

if ($openapi === null) {
    echo "Failed to decode JSON\n";
    exit;
}

echo "Total paths: " . count($openapi['paths']) . "\n";

$paths = [];
foreach ($openapi['paths'] as $path => $methods) {
    if (strpos($path, '/system') !== false) {
        $paths[$path] = array_keys($methods);
    }
}

print_r($paths);
