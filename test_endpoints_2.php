<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;

$firewall = Firewall::first();
$api = new PfSenseApiService($firewall);

function testEndpoint($api, $name, $method, $endpoint)
{
    echo "Testing $name ($method $endpoint)...\n";
    try {
        $response = $api->get($endpoint); // Using GET for discovery
        echo "SUCCESS: " . json_encode($response) . "\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Gateways Discovery ---\n";
testEndpoint($api, 'Gateways Plural', 'GET', '/routing/gateways');
testEndpoint($api, 'Gateways Status', 'GET', '/routing/gateway/status');

echo "\n--- Services Discovery ---\n";
testEndpoint($api, 'Services Status', 'GET', '/services/status');
testEndpoint($api, 'Service Status', 'GET', '/service/status');
testEndpoint($api, 'Services All', 'GET', '/services/all');
testEndpoint($api, 'Services List', 'GET', '/services/list');

echo "\n--- Ping Discovery ---\n";
// Ping is usually a POST, but let's see if we can find an endpoint first
testEndpoint($api, 'Ping Check', 'GET', '/diagnostics/ping');
testEndpoint($api, 'Ping Status', 'GET', '/diagnostics/ping/status');
