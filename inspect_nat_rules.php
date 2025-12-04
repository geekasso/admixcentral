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

$api = new PfSenseApiService($firewall);

echo "Fetching NAT Port Forwards...\n";
$natRules = $api->getNatPortForwards()['data'] ?? [];

echo "Fetching Firewall Rules...\n";
$filterRules = $api->getFirewallRules()['data'] ?? [];

echo "\n--- NAT Rules ---\n";
foreach ($natRules as $index => $rule) {
    if ($index === 4) {
        print_r($rule);
    }
    echo "Index: $index\n";
    echo "Description: " . ($rule['descr'] ?? 'N/A') . "\n";
    echo "Associated Rule ID: " . ($rule['associated-rule-id'] ?? 'N/A') . "\n";
    echo "----------------\n";
}

echo "\n--- Firewall Rules ---\n";
foreach ($filterRules as $index => $rule) {
    echo "Index: $index\n";
    echo "Description: " . ($rule['descr'] ?? 'N/A') . "\n";
    echo "Tracker: " . ($rule['tracker'] ?? 'N/A') . "\n";
    echo "Associated Rule ID: " . ($rule['associated-rule-id'] ?? 'N/A') . "\n";
    echo "----------------\n";
}
