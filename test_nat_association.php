<?php

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;

$firewall = Firewall::first();
if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

$api = new PfSenseApiService($firewall);

try {
    echo "Fetching NAT Port Forward rules...\n";
    $response = $api->getNatPortForwards();
    $rules = $response['data'] ?? [];

    if (empty($rules)) {
        echo "No rules found. Creating a test rule...\n";
        // Create a dummy rule to see default behavior
        $data = [
            'interface' => 'wan',
            'protocol' => 'tcp',
            'source' => 'any',
            'destination' => 'any',
            'destination_port' => '8080',
            'target' => '192.168.1.50',
            'local_port' => '80',
            'descr' => 'Test Rule for Association',
            'associated_rule_id' => 'pass', // Try 'pass'
        ];
        // We won't actually create it yet, just want to see if we can read existing ones.
        // But if none exist, we can't read.
        // Let's just print what we have.
    }

    foreach ($rules as $rule) {
        echo "Rule Description: " . ($rule['descr'] ?? 'N/A') . "\n";
        echo "Associated Rule ID: " . ($rule['associated-rule-id'] ?? 'N/A') . "\n";
        echo "Filter Rule Association: " . ($rule['filter-rule-association'] ?? 'N/A') . "\n";
        print_r($rule);
        echo "--------------------------------------------------\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
