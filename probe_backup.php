<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = \App\Models\Firewall::first();
if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

$api = new \App\Services\PfSenseApiService($firewall);

echo "Probing Backup Endpoints for {$firewall->name} ({$firewall->url})...\n";

$endpoints = [
    '/api/v1/diagnostics/backup',
    '/api/v1/system/backup',
    '/api/v1/system/configuration',
    '/api/v1/diagnostics/config',
    '/api/v2/diagnostics/backup', // Legacy check
    '/diagnostics/backup',        // Original
];

// Also check a known good endpoint to verify auth/connectivity
$endpoints[] = '/api/v1/system/status';
$endpoints[] = '/api/v1/diagnostics/tables';

foreach ($endpoints as $endpoint) {
    echo "--------------------------------------------------\n";
    echo "Probing: $endpoint\n";
    try {
        $response = $api->get($endpoint);
        echo "STATUS: SUCCESS (200)\n";
        // echo "Content: " . substr(json_encode($response), 0, 100) . "...\n";
    } catch (\Exception $e) {
        $code = $e->getCode();
        $msg = $e->getMessage();
        echo "STATUS: FAILED ($code)\n";
        echo "Message: " . substr($msg, 0, 200) . "\n";
    }
}
echo "--------------------------------------------------\n";
