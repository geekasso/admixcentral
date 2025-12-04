<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;

// Get the first firewall (assuming it's the one we're testing)
$firewall = Firewall::first();

if (!$firewall) {
    die("No firewall found in database.\n");
}

echo "Testing against firewall: " . $firewall->name . " (" . $firewall->ip_address . ")\n";

$api = new PfSenseApiService($firewall);

// 1. List Aliases
echo "\n1. Listing Aliases...\n";
try {
    $response = $api->get('/firewall/alias');
    $aliases = $response['data'] ?? [];
    echo "Found " . count($aliases) . " aliases.\n";
    foreach (array_slice($aliases, 0, 3) as $alias) {
        echo " - " . ($alias['name'] ?? 'unnamed') . " (" . ($alias['type'] ?? 'unknown') . ")\n";
    }
} catch (Exception $e) {
    die("Failed to list aliases: " . $e->getMessage() . "\n");
}

// 2. Create Test Alias
$testAliasName = "TestAlias_" . time();
echo "\n2. Creating Test Alias '$testAliasName'...\n";
$data = [
    'name' => $testAliasName,
    'type' => 'host',
    'descr' => 'Automated test alias',
    'address' => ['1.2.3.4', '5.6.7.8'],
    'detail' => ['Test Entry 1', 'Test Entry 2']
];

try {
    $api->post('/firewall/alias', $data);
    echo "Alias created successfully.\n";
} catch (Exception $e) {
    die("Failed to create alias: " . $e->getMessage() . "\n");
}

// 3. Verify Creation
echo "\n3. Verifying Alias Creation...\n";
try {
    $response = $api->get('/firewall/alias');
    $aliases = $response['data'] ?? [];
    $found = false;
    $createdAliasId = null;

    foreach ($aliases as $index => $alias) {
        if (($alias['name'] ?? '') === $testAliasName) {
            $found = true;
            $createdAliasId = $index; // API uses index for deletion usually, or name depending on endpoint
            // For v2 API, we might need to check how delete works. 
            // Usually it's by ID (index) or name. The controller uses ID.
            echo "Found created alias at index $index.\n";
            break;
        }
    }

    if (!$found) {
        die("Failed to find created alias in list.\n");
    }
} catch (Exception $e) {
    die("Failed to verify alias: " . $e->getMessage() . "\n");
}

// 4. Delete Test Alias
if ($found && $createdAliasId !== null) {
    echo "\n4. Deleting Test Alias (ID: $createdAliasId)...\n";
    try {
        // The controller uses the index ID for deletion
        $api->delete('/firewall/alias/' . $createdAliasId);
        echo "Alias deleted successfully.\n";
    } catch (Exception $e) {
        die("Failed to delete alias: " . $e->getMessage() . "\n");
    }
}

echo "\n✅ Firewall Aliases Verification Complete!\n";
