<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\PfSenseApiService;
use App\Models\Firewall;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = Firewall::first();
$api = new PfSenseApiService($firewall);

$endpoints = [
    '/system/packages',
    '/system/package/available',
];

foreach ($endpoints as $endpoint) {
    echo "Fetching $endpoint ...\n";
    try {
        $resp = $api->get($endpoint);
        // Print only first 2 items to avoid huge output
        if (isset($resp['data']) && is_array($resp['data'])) {
            $resp['data'] = array_slice($resp['data'], 0, 2);
        }
        print_r($resp);
    } catch (\Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
    echo "--------------------------------------------------\n";
}
