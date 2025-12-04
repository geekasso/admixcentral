<?php

use App\Models\Firewall;
use App\Services\PfSenseApiService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = Firewall::find(1);
$api = new PfSenseApiService($firewall);

try {
    echo "Gateways:\n";
    $gateways = $api->getRoutingGateways();
    print_r($gateways);

    echo "\nStatic Routes:\n";
    $static_routes = $api->getRoutingStaticRoutes();
    print_r($static_routes);

    echo "\nGateway Groups:\n";
    $gateway_groups = $api->getRoutingGatewayGroups();
    print_r($gateway_groups);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
