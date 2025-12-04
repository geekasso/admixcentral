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
    '/system/webgui/settings',
    '/firewall/advanced_settings',
    '/system/notifications/email_settings',
    '/system/tunables',
    '/services/ssh',
    '/system/console',
];

foreach ($endpoints as $endpoint) {
    echo "Fetching $endpoint ...\n";
    try {
        $resp = $api->get($endpoint);
        print_r($resp);
    } catch (\Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
    echo "--------------------------------------------------\n";
}
