<?php

use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';

echo "Starting...\n";

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Probing /firewall/nat/port_forwards...\n";
    $response = Http::withOptions(['verify' => false])
        ->withBasicAuth('admin', 'pfsense')
        ->get('https://172.30.1.129:444/api/v2/firewall/nat/port_forwards');

    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";

    echo "Probing /firewall/nat/outbound...\n";
    $response = Http::withOptions(['verify' => false])
        ->withBasicAuth('admin', 'pfsense')
        ->get('https://172.30.1.129:444/api/v2/firewall/nat/outbound');

    echo "Status: " . $response->status() . "\n";

    echo "Probing /firewall/nat/one_to_one...\n";
    $response = Http::withOptions(['verify' => false])
        ->withBasicAuth('admin', 'pfsense')
        ->get('https://172.30.1.129:444/api/v2/firewall/nat/one_to_one');

    echo "Status: " . $response->status() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
