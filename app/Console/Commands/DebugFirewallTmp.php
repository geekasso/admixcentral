<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Firewall;
use App\Services\PfSenseApiService;

class DebugFirewallTmp extends Command
{
    protected $signature = 'debug:firewall-tmp';
    protected $description = 'Debug firewall tmp files and dirty state logic';

    public function handle()
    {
        $firewall = Firewall::first();
        if (!$firewall) {
            $this->error('No firewall found.');
            return;
        }

        $this->info("Checking firewall: {$firewall->name} ({$firewall->ip_address})");
        $this->info("Current DB is_dirty: " . ($firewall->is_dirty ? 'TRUE' : 'FALSE'));

        $api = new PfSenseApiService($firewall);

        $this->info("Calling getDirtyState()...");
        try {
            $isDirty = $api->getDirtyState();
            $this->info("getDirtyState() returned: " . ($isDirty ? 'TRUE' : 'FALSE'));

            if ($firewall->is_dirty !== $isDirty) {
                $this->info("Mismatch detected! Attempting to sync...");
                $firewall->is_dirty = $isDirty;
                $firewall->save();
                $this->info("Saved firewall. New is_dirty: " . ($firewall->fresh()->is_dirty ? 'TRUE' : 'FALSE'));
            } else {
                $this->info("No mismatch. DB matches API state.");
            }

        } catch (\Exception $e) {
            $this->error("Exception caught: " . $e->getMessage());
        }

        $response = $api->diagnosticsCommandPrompt('ls -1a /tmp');

        $this->info('API Response (ls -1a /tmp):');
        if (isset($response['data']['output'])) {
            $this->line($response['data']['output']);
        } else {
            $this->error('No output in response.');
            dump($response);
        }
    }
}
