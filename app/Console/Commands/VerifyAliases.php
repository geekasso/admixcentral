<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Firewall;
use App\Services\PfSenseApiService;

class VerifyAliases extends Command
{
    protected $signature = 'verify:aliases';
    protected $description = 'Verify Firewall Aliases CRUD operations against live pfSense';

    public function handle()
    {
        try {
            $logFile = base_path('verify_aliases_debug.txt');
            file_put_contents($logFile, "Starting verification...\n");

            $log = function ($message) use ($logFile) {
                file_put_contents($logFile, $message . "\n", FILE_APPEND);
            };

            $firewall = Firewall::first();
            if (!$firewall) {
                $log("No firewall found.");
                return 1;
            }

            $log("Testing against firewall: {$firewall->name} ({$firewall->ip_address})");
            $api = new PfSenseApiService($firewall);

            // 1. List Aliases
            $log("\n1. Listing Aliases...");
            $response = $api->get('/firewall/aliases');
            $aliases = $response['data'] ?? [];
            $log("Found " . count($aliases) . " aliases.");
            foreach (array_slice($aliases, 0, 3) as $alias) {
                $log(" - " . ($alias['name'] ?? 'unnamed') . " (" . ($alias['type'] ?? 'unknown') . ")");
            }

            // 2. Create Test Alias
            $testAliasName = "TestAlias_" . time();
            $log("\n2. Creating Test Alias '$testAliasName'...");
            $data = [
                'name' => $testAliasName,
                'type' => 'host',
                'descr' => 'Automated test alias',
                'address' => ['1.2.3.4', '5.6.7.8'],
                'detail' => ['Test Entry 1', 'Test Entry 2']
            ];

            $api->post('/firewall/alias', $data);
            $log("Alias created successfully.");

            // 3. Verify Creation
            $log("\n3. Verifying Alias Creation...");
            $response = $api->get('/firewall/aliases');
            $aliases = $response['data'] ?? [];
            $found = false;
            $createdAliasId = null;

            foreach ($aliases as $index => $alias) {
                if (($alias['name'] ?? '') === $testAliasName) {
                    $found = true;
                    $createdAliasId = $index;
                    $log("Found created alias at index $index.");
                    break;
                }
            }

            if (!$found) {
                $log("Failed to find created alias in list.");
                return 1;
            }

            // 4. Delete Test Alias
            if ($found && $createdAliasId !== null) {
                $log("\n4. Deleting Test Alias (ID: $createdAliasId)...");
                $api->delete('/firewall/alias', ['id' => $createdAliasId]);
                $log("Alias deleted successfully.");
            }

            $log("\n✅ Firewall Aliases Verification Complete!");
            return 0;

        } catch (\Throwable $e) {
            $logFile = base_path('verify_aliases_debug.txt');
            file_put_contents($logFile, "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString(), FILE_APPEND);
            return 1;
        }
    }
}
