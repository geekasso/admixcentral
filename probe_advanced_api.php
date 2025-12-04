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

$candidates = [
    '/system/tunable',
];

foreach ($candidates as $endpoint) {
    echo "Testing $endpoint ... ";
    try {
        $resp = $api->get($endpoint);
        echo "OK\n";
    } catch (\Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
}
