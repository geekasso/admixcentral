<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get firewalls based on user role
        // Get firewalls based on user role
        if ($user->isGlobalAdmin()) {
            $firewalls = Firewall::with('company')->get();
        } else {
            $firewalls = Firewall::where('company_id', $user->company_id)->get();
        }

        // Collect status for each firewall
        // $firewallsWithStatus = $firewalls->map(function ($firewall) {
        //     try {
        //         $api = new PfSenseApiService($firewall);
        //         $status = $api->getSystemStatus();
        //         $firewall->status = $status;
        //         $firewall->online = true;
        //     } catch (\Exception $e) {
        //         $firewall->status = null;
        //         $firewall->online = false;
        //         $firewall->error = $e->getMessage();
        //     }
        //     return $firewall;
        // });

        return view('dashboard', ['firewallsWithStatus' => $firewalls]);
    }

    public function checkStatus(Firewall $firewall)
    {
        // Strategy: Live-First, Cache-Fallback
        // 1. Attempt to fetch Static Data (Version, BIOS) LIVE.
        //    - Success: Update Cache.
        //    - Fail: Read from Cache.
        // 2. Attempt to fetch Dynamic Data (Status) LIVE.
        //    - Success: Merge with Static and return.
        //    - Fail: Return Offline (but include Static info if available so user sees device details).

        $staticCacheKey = 'firewall_static_info_' . $firewall->id;
        $api = new PfSenseApiService($firewall);
        $staticInfo = [];
        $staticFetchSuccess = false;

        // 1. Try Live Static Fetch
        try {
            $versionInfo = $api->getSystemVersion();
            if (isset($versionInfo['data'])) {
                $staticInfo = array_merge($staticInfo, $versionInfo['data']);
            }

            try {
                $apiVersionResponse = $api->getApiVersion();
                $apiVersion = $apiVersionResponse['data']['output'] ?? 'Unknown';
                $staticInfo['api_version'] = trim($apiVersion);
            } catch (\Exception $e) {
                $staticInfo['api_version'] = 'N/A';
            }

            // Success! Update Cache (valid for 24h as backup)
            \Illuminate\Support\Facades\Cache::put($staticCacheKey, $staticInfo, now()->addDay());
            $staticFetchSuccess = true;

        } catch (\Exception $e) {
            // Live Static Key fetch failed? Fallback to cache
            $staticInfo = \Illuminate\Support\Facades\Cache::get($staticCacheKey, []);
        }

        // 2. Try Live Dynamic Fetch
        try {
            $dynamicStatus = $api->getSystemStatus();

            // Fetch Interface Status (for Traffic Monitor & Status Indicators)
            try {
                $interfaces = $api->getInterfacesStatus();
                // DEBUG: Log interfaces to check for name/label fields
                // \Illuminate\Support\Facades\Log::info('Interfaces Status Structure:', ['data' => $interfaces['data'] ?? []]);
                $dynamicStatus['interfaces'] = $interfaces['data'] ?? [];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Interface Status Fetch Failed: ' . $e->getMessage());
                $dynamicStatus['interfaces'] = [];
            }

            // Fetch DNS Servers
            try {
                $dns = $api->getSystemDns();
                // Structure depends on API: usually data->dns or data->dnsserver
                // Let's assume standardized response ['data' => ['dns' => [...]]] or similar
                // If using pfSense-API (faucets), it returns ['data' => ['dns_server' => [...]]]
                if (isset($dns['data']['dns'])) {
                    $dynamicStatus['data']['dns_servers'] = $dns['data']['dns'];
                } elseif (isset($dns['data']['dns_server'])) {
                    $dynamicStatus['data']['dns_servers'] = $dns['data']['dns_server'];
                } elseif (isset($dns['data']['dnsserver'])) {
                    $dynamicStatus['data']['dns_servers'] = $dns['data']['dnsserver']; // legacy
                } else {
                    $dynamicStatus['data']['dns_servers'] = [];
                }
            } catch (\Exception $e) {
                // Ignore missing DNS
            }



            // Fetch Config History (Last Change)
            try {
                $history = $api->getConfigHistory();

                // Expecting data to be an array of revisions
                $revisions = $history['data'] ?? $history ?? [];

                if (!empty($revisions) && is_array($revisions)) {
                    // Get first item
                    $latest = reset($revisions);
                    if (isset($latest['time'])) {
                        // It's likely a unix timestamp
                        $dynamicStatus['data']['last_config_change'] = date('Y-m-d H:i:s T', $latest['time']);
                        $dynamicStatus['data']['last_config_change_ts'] = $latest['time'];
                    } elseif (isset($latest['date'])) {
                        $dynamicStatus['data']['last_config_change'] = $latest['date'];
                    }
                }
            } catch (\Exception $e) {
                // Ignore
            }

            // Merge Static Info into Dynamic Status
            if (isset($dynamicStatus['data'])) {
                $dynamicStatus['data'] = array_merge($dynamicStatus['data'], $staticInfo);
                if (isset($staticInfo['api_version'])) {
                    $dynamicStatus['api_version'] = $staticInfo['api_version'];
                }
            } else {
                $dynamicStatus = array_merge($dynamicStatus ?? [], $staticInfo);
            }

            // Broadcast
            event(new \App\Events\DeviceStatusUpdateEvent($firewall, $dynamicStatus));

            return response()->json([
                'online' => true,
                'status' => $dynamicStatus,
                'source' => $staticFetchSuccess ? 'live_full' : 'live_dynamic_cached_static'
            ]);

        } catch (\Exception $e) {
            // Device is completely unreachable (or at least status check failed)
            // Return OFFLINE status, but include the Cached Static Info so the dashboard isn't empty
            return response()->json([
                'online' => false,
                'error' => $e->getMessage(),
                'status' => ['data' => $staticInfo] // Pass static info even if offline
            ]);
        }
    }

    public function firewall(Request $request, Firewall $firewall)
    {
        // Data is now fetched asynchronously via AJAX to prevent page load delays/beeps
        return view('firewall.dashboard', compact('firewall'));
    }
}
