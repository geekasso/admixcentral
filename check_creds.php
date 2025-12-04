<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firewall = \App\Models\Firewall::find(1);

if ($firewall) {
    echo "ID: " . $firewall->id . "\n";
    echo "API Key: " . ($firewall->api_key ? 'SET' : 'NULL') . "\n";
    echo "API Secret: " . ($firewall->api_secret ? 'SET' : 'NULL') . "\n";
    echo "URL: " . $firewall->url . "\n";
} else {
    echo "Firewall not found.\n";
}
