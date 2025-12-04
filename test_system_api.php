<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\PfSenseApiService;
use App\Models\Firewall;
use Illuminate\Support\Facades\Crypt;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the first firewall
$firewall = Firewall::first();

if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

echo "Testing System API for firewall: " . $firewall->name . "\n";

$api = new PfSenseApiService($firewall);

$endpoints = [
    '/system/config',
    '/system/general',
    '/system/hostname',
    '/system/domain',
    '/system/timezone',
    '/system/dns',
    '/status/system',
];

foreach ($endpoints as $endpoint) {
    echo "Testing endpoint: $endpoint ... ";
    try {
        $response = $api->get($endpoint);
        echo "OK (Status: " . ($response['status'] ?? 'Unknown') . ")\n";
        print_r($response);
    } catch (\Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
}
