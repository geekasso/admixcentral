<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\PfSenseApiService;
use App\Models\Firewall;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = Firewall::first();
$api = new PfSenseApiService($firewall);

echo "Testing GET /system/advanced ... ";
try {
    $resp = $api->get('/system/advanced');
    echo "OK\n";
    print_r($resp);
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
