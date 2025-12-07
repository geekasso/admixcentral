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

echo "Probing REST API Endpoints for {$firewall->name}...\n";

$endpoints = [
    '/api/v1/system/api',
    '/api/v1/system/api/version',
    '/api/v1/system/api/update',
    '/api/v1/system/package/pfsense-api',
    '/api/v1/system/package/pfSense-pkg-RESTAPI',
    '/system/api/update',
    '/api/v2/system/api/update',
    '/api/v1/system/console/command', // Can we run 'pkg install ...' ?
];

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
        echo "Message: " . substr($msg, 0, 100) . "\n";
    }
}
echo "--------------------------------------------------\n";
