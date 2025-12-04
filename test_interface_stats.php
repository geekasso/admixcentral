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

echo "Testing Interface Stats for firewall: " . $firewall->name . "\n";

$api = new PfSenseApiService($firewall);

echo "1. Testing getInterfaces() ...\n";
try {
    $interfaces = $api->getInterfaces();
    echo "Keys in first interface: " . implode(', ', array_keys(reset($interfaces))) . "\n";
    // Check for stats keys
    $hasStats = false;
    foreach (reset($interfaces) as $key => $value) {
        if (strpos($key, 'stats') !== false || in_array($key, ['inbytes', 'outbytes', 'inpkts', 'outpkts'])) {
            $hasStats = true;
            break;
        }
    }
    echo "Has stats? " . ($hasStats ? "Yes" : "No") . "\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

echo "\n2. Testing /status/interfaces ...\n";
try {
    $status = $api->get('/status/interfaces');
    echo "OK (Status: " . ($status['status'] ?? 'Unknown') . ")\n";
    if (isset($status['data'])) {
        echo "Keys in first status item: " . implode(', ', array_keys(reset($status['data']))) . "\n";
    }
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

echo "\n3. Testing /status/interface_stats ...\n";
try {
    $status = $api->get('/status/interface_stats');
    echo "OK\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
