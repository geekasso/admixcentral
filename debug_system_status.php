<?php

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = Firewall::find(1);
if (!$firewall) {
    echo "Firewall not found\n";
    exit;
}

$api = new PfSenseApiService($firewall);
try {
    $status = $api->getSystemStatus();
    echo "System Status Response:\n";
    print_r($status);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
