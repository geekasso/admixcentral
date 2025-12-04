<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Http;

$firewall = Firewall::first();

if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

echo "Testing VLAN API endpoints for firewall: {$firewall->name} ({$firewall->url})\n";

$endpoints = [
    '/auth/keys', // Test auth first
    '/interface/vlan',
    '/interface/vlans',
];

$api = new PfSenseApiService($firewall);

foreach ($endpoints as $endpoint) {
    echo "Testing $endpoint... ";
    try {
        // We use a reflection or just a raw request if possible, but PfSenseApiService has protected request method.
        // So we'll use the public 'get' method.
        $response = $api->get($endpoint);
        echo "SUCCESS! Found data.\n";
        // print_r($response);
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), '404') !== false) {
            echo "404 Not Found\n";
        } else {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
