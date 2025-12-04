<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Log;

// Ensure logging goes to stderr for visibility or we check the file
Log::setDefaultDriver('stack');

$firewall = Firewall::find(1);

if (!$firewall) {
    echo "Firewall #1 not found.\n";
    exit(1);
}

echo "Firewall: " . $firewall->name . "\n";
echo "URL: " . $firewall->url . "\n";

$api = new PfSenseApiService($firewall);

echo "Testing GET /services/dhcp_server with id=lan...\n";

try {
    $response = $api->get('/services/dhcp_server', ['id' => 'lan']);
    echo "Response Status: " . ($response['status'] ?? 'unknown') . "\n";
    echo "Response Code: " . ($response['code'] ?? 'unknown') . "\n";
    print_r($response);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
