<?php

$json = file_get_contents('openapi.json');
if ($json === false) {
    echo "Failed to read file\n";
    exit(1);
}
echo "Read " . strlen($json) . " bytes\n";
$data = json_decode($json, true);

if (!$data) {
    echo "Failed to parse JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

$paths = array_keys($data['paths'] ?? []);
$paths = array_keys($data['paths'] ?? []);
echo "Total paths: " . count($paths) . "\n";
foreach ($paths as $path) {
    echo $path . "\n";
}


