<?php

$json = file_get_contents('openapi.json');
$data = json_decode($json, true);

if (!$data) {
    echo "Failed to parse JSON\n";
    exit(1);
}

$paths = array_keys($data['paths'] ?? []);
echo "Total paths: " . count($paths) . "\n";

$systemPaths = array_filter($paths, function ($path) {
    return strpos($path, '/system') === 0;
});

sort($systemPaths);

foreach ($systemPaths as $path) {
    echo $path . "\n";
}
