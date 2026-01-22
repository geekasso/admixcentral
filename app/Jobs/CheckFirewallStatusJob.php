<?php

namespace App\Jobs;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckFirewallStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $firewall;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Firewall $firewall)
    {
        $this->firewall = $firewall;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firewall = $this->firewall;
        $api = new PfSenseApiService($firewall);

        try {
            $data = $api->refreshSystemStatus();

            $status = [
                'online' => true,
                'data' => $data,
                'api_version' => $data['api_version'] ?? ($data['data']['api_version'] ?? null),
                'updated_at' => now()->toIso8601String(),
            ];

            // Cache
            Cache::put('firewall_status_' . $firewall->id, $status, now()->addDay());

            // Broadcast
            event(new \App\Events\DeviceStatusUpdateEvent($firewall, $status));

        } catch (\Exception $e) {
            // Offline
            $offlineStatus = [
                'online' => false, 
                'data' => null, 
                'error' => $e->getMessage(),
                'updated_at' => now()->toIso8601String(),
            ];
            
            // Cache Offline Status
            Cache::put('firewall_status_' . $firewall->id, $offlineStatus, now()->addDay());

            // Broadcast
            event(new \App\Events\DeviceStatusUpdateEvent($firewall, $offlineStatus));
        }
    }
}
