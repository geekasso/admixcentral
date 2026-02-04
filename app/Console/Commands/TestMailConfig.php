<?php

namespace App\Console\Commands;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Console\Command;

class TestMailConfig extends Command
{
    protected $signature = 'debug:mail-config {firewall_id} {--username=} {--password=} {--auth-mech=}';
    protected $description = 'Fetch and display mail notification settings for a firewall, or update them';

    public function handle()
    {
        $id = $this->argument('firewall_id');
        $firewall = Firewall::find($id);

        if (!$firewall) {
            $this->error("Firewall not found");
            return;
        }

        $api = new PfSenseApiService($firewall);

        // Update handling
        if ($this->option('username') || $this->option('password') || $this->option('auth-mech')) {
            $updateData = [];
            $user = $this->option('username');
            $pass = $this->option('password');

            if ($user) {
                $updateData['username'] = $user;
            }
            if ($pass) {
                $updateData['password'] = $pass;
            }

            // Set default auth mechanism to LOGIN if not provided, as per schema condition
            if ($this->option('auth-mech')) {
                $updateData['authentication_mechanism'] = $this->option('auth-mech');
            } else if ($user || $pass) {
                $updateData['authentication_mechanism'] = 'LOGIN';
            }

            $this->info("Attempting update with: " . json_encode($updateData));
            try {
                $response = $api->updateSystemNotifications($updateData);
                $this->info("Update response: " . json_encode($response, JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
                $this->error("Update failed: " . $e->getMessage());
            }
        }

        try {
            $this->info("Fetching current mail settings for {$firewall->name}...");
            $response = $api->getSystemNotifications();
            $this->info(json_encode($response, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
