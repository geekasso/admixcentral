<?php
$json = file_get_contents('/home/baga/Code/admixcenter/openapi.json');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    exit;
}

if (!isset($data['paths'])) {
    echo "No paths found\n";
    exit;
}

foreach ($data['paths'] as $path => $details) {
    if (strpos($path, '/diagnostics') !== false || strpos($path, 'ping') !== false) {
        echo $path . "\n";
    }
}
