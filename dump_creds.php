<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;

$firewall = Firewall::first();

if ($firewall) {
    echo "API Key: " . $firewall->api_key . "\n";
    echo "API Secret: " . $firewall->api_secret . "\n";
} else {
    echo "No firewall found.\n";
}
