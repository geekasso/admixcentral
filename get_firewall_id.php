<?php
echo "Script starting...\n";
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $firewall = App\Models\Firewall::first();
    if ($firewall) {
        echo "Firewall ID: " . $firewall->id . "\n";
    } else {
        echo "No firewall found. Creating one...\n";
        $firewall = App\Models\Firewall::create([
            'name' => 'Test Firewall',
            'host' => '172.30.1.129',
            'port' => 444,
            'username' => 'admin',
            'password' => 'pfsense', // This might need encryption depending on model
            'api_key' => 'test',
            'api_secret' => 'test'
        ]);
        echo "Created Firewall ID: " . $firewall->id . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
