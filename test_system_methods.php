<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\PfSenseApiService;
use App\Models\Firewall;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = Firewall::first();
$api = new PfSenseApiService($firewall);

$endpoint = '/system/hostname';
$data = ['hostname' => 'pfSense', 'domain' => 'home.arpa'];

echo "Testing POST $endpoint...\n";
try {
    $api->post($endpoint, $data);
    echo "POST Success\n";
} catch (\Exception $e) {
    echo "POST Failed: " . $e->getMessage() . "\n";
}

echo "Testing PUT $endpoint...\n";
try {
    $api->put($endpoint, $data);
    echo "PUT Success\n";
} catch (\Exception $e) {
    echo "PUT Failed: " . $e->getMessage() . "\n";
}

echo "Testing PATCH $endpoint...\n";
try {
    $api->patch($endpoint, $data);
    echo "PATCH Success\n";
} catch (\Exception $e) {
    echo "PATCH Failed: " . $e->getMessage() . "\n";
}
