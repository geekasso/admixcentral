<?php

namespace App\Listeners;

use App\Events\DeviceStatusUpdateEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheDeviceStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeviceStatusUpdateEvent $event): void
    {
        // Logic moved to CheckFirewallStatusJob to prevent cache structure conflicts.
        // Keeping this listener empty/disabled for now.
    }
}
