<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\PfSenseApiService;
use App\Models\Firewall;

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

echo "Testing Traceroute API for firewall: " . $firewall->name . "\n";

$api = new PfSenseApiService($firewall);

$endpoints = [
    '/diagnostics/traceroute',
    '/diagnostics/ping',
    '/diagnostics/dns_lookup',
];

foreach ($endpoints as $endpoint) {
    echo "Testing endpoint: $endpoint ... ";
    try {
        // Try GET first
        $response = $api->get($endpoint);
        echo "GET OK (Status: " . ($response['status'] ?? 'Unknown') . ")\n";
    } catch (\Exception $e) {
        echo "GET Failed: " . $e->getMessage() . "\n";

        // Try POST (some diagnostics require POST with params)
        try {
            $response = $api->post($endpoint, ['host' => '8.8.8.8']);
            echo "POST OK (Status: " . ($response['status'] ?? 'Unknown') . ")\n";
        } catch (\Exception $e2) {
            echo "POST Failed: " . $e2->getMessage() . "\n";
        }
    }
}
