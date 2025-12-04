<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;

$firewall = Firewall::first();

if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

echo "Testing Rules API for firewall: {$firewall->name} ({$firewall->url})\n";

$api = new PfSenseApiService($firewall);

try {
    $response = $api->getFirewallRules();
    echo "SUCCESS! Found " . count($response['data']) . " rules.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
