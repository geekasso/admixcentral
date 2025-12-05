<?php
ob_start();
$json = file_get_contents('openapi.json');
if ($json === false) {
    echo "Failed to read openapi.json\n";
    exit;
}
$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    exit;
}

echo "Checking Command Prompt schema...\n";
if (isset($data['paths']['/api/v2/diagnostics/command_prompt']['post'])) {
    print_r($data['paths']['/api/v2/diagnostics/command_prompt']['post']);
}

echo "Checking Reboot schema...\n";
if (isset($data['paths']['/api/v2/diagnostics/reboot']['post'])) {
    print_r($data['paths']['/api/v2/diagnostics/reboot']['post']);
}

echo "Checking Tables schema...\n";
if (isset($data['paths']['/api/v2/diagnostics/tables']['get'])) {
    print_r($data['paths']['/api/v2/diagnostics/tables']['get']);
}
file_put_contents('openapi_debug.txt', ob_get_clean());
