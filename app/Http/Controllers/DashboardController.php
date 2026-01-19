<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Models\User;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     *
     * This method gathers data for the dashboard view, including:
     * - A list of firewalls (filtered by user role).
     * - Aggregated statistics (total firewalls, companies, offline devices, etc.).
     * - A system health score calculated from the cached CPU and Memory usage of all firewalls.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get firewalls based on user role
        // Fetch firewalls based on User Role.
        // Global Admins see all firewalls; other users see only their company's firewalls.
        if ($user->isGlobalAdmin()) {
            $firewalls = Firewall::with('company')->orderBy('name')->get();
        } else {
            $firewalls = Firewall::where('company_id', $user->company_id)->orderBy('name')->get();
        }

        // Attach cached status data to each firewall instance for display.
        // This avoids N+1 API calls on the dashboard load.
        $firewalls->each(function ($firewall) {
            $cached = Cache::get('firewall_status_' . $firewall->id);
            if ($cached && isset($cached['data'])) {
                $firewall->cached_status = $cached['data'];
            } else {
                $firewall->cached_status = null;
            }
        });

        // Calculate Widgets Data
        $totalFirewalls = $firewalls->count();
        // For companies, if admin -> all, else -> owned. 
        // Since $firewalls is already scoped, we can just count unique company IDs in the collection
        // OR fetching fresh if we want "Total Companies on Platform" vs "My Companies". 
        // Assuming user wants to see "Companies Managed":
        $totalCompanies = $firewalls->pluck('company_id')->unique()->count();

        // Offline Devices (No Active WebSocket Connection)
        // Since we already have the collection, we can iterate or fetch count separately for performance if collection is huge.
        // For Dashboard, separate query is often cleaner/faster than hydrating relationships just for a count.
        if ($user->isGlobalAdmin()) {
            $offlineFirewalls = Firewall::whereDoesntHave('activeConnection')->count();
        } else {
            $offlineFirewalls = Firewall::where('company_id', $user->company_id)
                ->whereDoesntHave('activeConnection')
                ->count();
        }

        // Total registered users
        // Total registered users
        $totalUsers = User::count();
        // Count Global Admins (users with role 'admin' and no specific company assignment)
        $totalAdmins = User::where('role', 'admin')->whereNull('company_id')->count();

        // Calculate System Health Score based on average CPU/Memory from cached status
        $healthStatus = 'No Data';
        $healthColor = 'gray';
        $avgCpu = 0;
        $avgMemory = 0;

        // ... (existing code for avgCpu/avgMemory calculation if needed to stay same) ...
        // Wait, I need to make sure I don't break the flow. The previous view logic is unchanged.
        
        // Filter firewalls with cached status
        $firewallsWithData = $firewalls->filter(function ($firewall) {
            return $firewall->cached_status !== null &&
                is_array($firewall->cached_status) &&
                (isset($firewall->cached_status['cpu_usage']) || isset($firewall->cached_status['mem_usage']));
        });

        if ($firewallsWithData->count() > 0) {
            $totalCpu = 0;
            $totalMemory = 0;
            $cpuCount = 0;
            $memCount = 0;

            // Iterate through firewalls to sum up CPU and Memory usage.
            foreach ($firewallsWithData as $firewall) {
                $status = $firewall->cached_status;

                if (isset($status['cpu_usage'])) {
                    $totalCpu += floatval($status['cpu_usage']);
                    $cpuCount++;
                }

                if (isset($status['mem_usage'])) {
                    $totalMemory += floatval($status['mem_usage']);
                    $memCount++;
                }
            }

            // Calculate averages if data exists
            if ($cpuCount > 0 || $memCount > 0) {
                $avgCpu = $cpuCount > 0 ? round($totalCpu / $cpuCount, 1) : 0;
                $avgMemory = $memCount > 0 ? round($totalMemory / $memCount, 1) : 0;

                // Determine health based on the worse of the two metrics.
                // < 50% = Excellent (Green)
                // < 70% = Good (Blue)
                // < 85% = Fair (Yellow)
                // >= 85% = Critical (Red)
                $maxUsage = max($avgCpu, $avgMemory);

                if ($maxUsage < 50) {
                    $healthStatus = 'Excellent';
                    $healthColor = 'green';
                } elseif ($maxUsage < 70) {
                    $healthStatus = 'Good';
                    $healthColor = 'blue';
                } elseif ($maxUsage < 85) {
                    $healthStatus = 'Fair';
                    $healthColor = 'yellow';
                } else {
                    $healthStatus = 'Critical';
                    $healthColor = 'red';
                }
            }
        }

        return view('dashboard', [
            'firewallsWithStatus' => $firewalls,
            'totalFirewalls' => $totalFirewalls,
            'totalCompanies' => $totalCompanies,
            'offlineFirewalls' => $offlineFirewalls,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'avgCpu' => $avgCpu,
            'avgMemory' => $avgMemory,
            'healthStatus' => $healthStatus,
            'healthColor' => $healthColor
        ]);
    }

    /**
     * AJAX endpoint to fetch real-time status for a specific firewall.
     *
     * Strategy: Live-First, Cache-Fallback
     * 1. Attempt to fetch Static Data (Version, BIOS) LIVE.
     *    - Success: Update Cache.
     *    - Fail: Read from Cache.
     * 2. Attempt to fetch Dynamic Data (Status, Interfaces, Gateways) LIVE.
     *    - Success: Merge with Static and return.
     *    - Fail: Return Offline status (but include Static info if available so user sees device details).
     *
     * @param Firewall $firewall
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Firewall $firewall)
    {
        // 1. Setup Service and Keys
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

            // Fetch Installed Packages Count
            try {
                $packages = $api->getSystemPackages();
                // Expecting structure: ['data' => ['package' => [...]]] or just array of packages
                // pfSense API usually returns ['data' => ['package' => [ ... list of packages ... ]]]
                
                $pkgCount = 0;
                if (isset($packages['data']['package']) && is_array($packages['data']['package'])) {
                    $pkgCount = count($packages['data']['package']);
                } elseif (isset($packages['data']) && is_array($packages['data'])) {
                    // Fallback for some versions/endpoints
                    $pkgCount = count($packages['data']);
                }
                
                $dynamicStatus['data']['installed_packages_count'] = $pkgCount;

            } catch (\Exception $e) {
                $dynamicStatus['data']['installed_packages_count'] = 'N/A';
            }

            // Fetch Gateways Status (for Dashboard Cards)
            try {
                // We need gateway status (online/offline/latency) AND description (from config)
                // This logic mirrors StatusController::gateways()
                
                // 1. Fetch Status (runtime info like 'status', 'monitorip', 'delay')
                $statusData = [];
                $rawStatus = $api->getGateways(); // This maps to /status/gateways
                if (isset($rawStatus['data']['gateway']) && is_array($rawStatus['data']['gateway'])) {
                    $statusData = $rawStatus['data']['gateway'];
                } elseif (isset($rawStatus['data']) && is_array($rawStatus['data'])) {
                    $statusData = $rawStatus['data'];
                }

                // 2. Fetch Config (static info like 'descr')
                $configData = [];
                $rawConfig = $api->getRoutingGateways(); // This maps to /routing/gateways
                if (isset($rawConfig['data']['gateway']) && is_array($rawConfig['data']['gateway'])) {
                    $configData = $rawConfig['data']['gateway'];
                } elseif (isset($rawConfig['data']) && is_array($rawConfig['data'])) {
                    $configData = $rawConfig['data'];
                }

                // 3. Merge Description from Config into Status
                // If status is empty (e.g. API fail), we might want to fallback to config-only, but status is critical for the badge.
                // Prioritize Status data.
                foreach ($statusData as &$gateway) {
                    $config = collect($configData)->firstWhere('name', $gateway['name']);
                    if ($config) {
                        $gateway['descr'] = $config['descr'] ?? '';
                    }
                }
                unset($gateway); // break reference

                $dynamicStatus['gateways'] = $statusData;

            } catch (\Exception $e) {
                $dynamicStatus['gateways'] = [];
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
