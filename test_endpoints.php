<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Log;

$firewall = Firewall::first();
if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

$api = new PfSenseApiService($firewall);

function testEndpoint($api, $name, $method, $endpoint, $data = [])
{
    echo "Testing $name ($method $endpoint)...\n";
    try {
        if ($method === 'GET') {
            $response = $api->get($endpoint, $data);
        } elseif ($method === 'POST') {
            $response = $api->post($endpoint, $data);
        } elseif ($method === 'PUT') {
            $response = $api->put($endpoint, $data);
        } elseif ($method === 'PATCH') {
            $response = $api->patch($endpoint, $data);
        } elseif ($method === 'DELETE') {
            $response = $api->delete($endpoint, $data);
        }
        echo "SUCCESS: " . json_encode($response) . "\n";
        return $response;
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        return null;
    }
}

// 1. Status Dashboard Endpoints
echo "\n--- Status Dashboard ---\n";
testEndpoint($api, 'System Version', 'GET', '/system/version');
testEndpoint($api, 'Interfaces (Singular)', 'GET', '/interface'); // Suspected fail
testEndpoint($api, 'Interfaces (Plural)', 'GET', '/interfaces'); // Suspected success
testEndpoint($api, 'Gateways', 'GET', '/routing/gateway');
testEndpoint($api, 'Services', 'GET', '/services');

// 2. Schedules CRUD
echo "\n--- Schedules ---\n";
$scheduleName = "TestSched_" . time();
$createData = [
    'name' => $scheduleName,
    'descr' => 'Test Description',
    'timerange' => [
        ['month' => '1', 'day' => '1', 'hour' => '0:00-23:59']
    ]
];

$created = testEndpoint($api, 'Create Schedule', 'POST', '/firewall/schedule', $createData);

if ($created && isset($created['data']['id'])) { // Assuming ID is returned, or we use name?
    // pfSense API often uses ID or Name. Let's check response.
    // If response doesn't have ID, we might need to list to find it.

    // List to find ID
    $schedules = testEndpoint($api, 'List Schedules', 'GET', '/firewall/schedules');
    $id = null;
    if ($schedules && isset($schedules['data'])) {
        foreach ($schedules['data'] as $s) {
            if ($s['name'] === $scheduleName) {
                $id = $s['id'] ?? null; // Some endpoints use 'name' as ID?
                // If no ID field, maybe we use index? Or name?
                // Let's assume ID for now.
                break;
            }
        }
    }

    if ($id !== null) {
        echo "Found Schedule ID: $id\n";

        // Update
        $updateData = ['descr' => 'Updated Description'];
        testEndpoint($api, 'Update Schedule (ID in Query)', 'PATCH', "/firewall/schedule?id=$id", $updateData);

        // Delete
        testEndpoint($api, 'Delete Schedule (ID in Body)', 'DELETE', "/firewall/schedule", ['id' => $id]);
    } else {
        echo "Could not find ID for created schedule.\n";
    }
}

// 3. Diagnostics Ping
echo "\n--- Diagnostics Ping ---\n";
testEndpoint($api, 'Ping (Default)', 'POST', '/diagnostics/ping', ['host' => '8.8.8.8']);
testEndpoint($api, 'Ping (Interface)', 'POST', '/diagnostics/interface/ping', ['host' => '8.8.8.8']);

