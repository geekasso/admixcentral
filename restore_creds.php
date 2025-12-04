<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $firewall = \App\Models\Firewall::find(1);
    if ($firewall) {
        $firewall->api_key = 'admin';
        $firewall->api_secret = 'pfsense';
        $firewall->save();
        echo "Credentials restored for Firewall ID 1.\n";
        echo "API Key: " . $firewall->api_key . "\n";
    } else {
        echo "Firewall ID 1 not found.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
