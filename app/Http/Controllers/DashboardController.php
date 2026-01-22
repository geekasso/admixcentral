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
     * Use shared logic with Background Jobs.
     *
     * @param Firewall $firewall
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Firewall $firewall)
    {
        // Close session write lock to allow concurrent requests
        session_write_close();

        try {
            $api = new PfSenseApiService($firewall);
            
            // Use the shared service method (Same as Job)
            $dynamicStatus = $api->refreshSystemStatus();

            // Broadcast so WebSocket listeners get it too
            $statusEventData = [
                'online' => true,
                'data' => $dynamicStatus,
                'api_version' => $dynamicStatus['api_version'] ?? null,
                'updated_at' => now()->toIso8601String()
            ];

            event(new \App\Events\DeviceStatusUpdateEvent($firewall, $statusEventData));

            return response()->json([
                'online' => true,
                'status' => $dynamicStatus,
                'source' => 'live_sync'
            ]);

        } catch (\Exception $e) {
            $staticCacheKey = 'firewall_static_info_' . $firewall->id;
            $staticInfo = Cache::get($staticCacheKey, []);

            // Broadcast offline event
            event(new \App\Events\DeviceStatusUpdateEvent($firewall, [
                'online' => false, 
                'error' => $e->getMessage(),
                'updated_at' => now()->toIso8601String(),
                'data' => null // Consistent with Job
            ]));

            return response()->json([
                'online' => false,
                'error' => $e->getMessage(),
                'status' => ['data' => $staticInfo] 
            ]);
        }
    }

    public function firewall(Request $request, Firewall $firewall)
    {
        // Data is now fetched asynchronously via AJAX to prevent page load delays/beeps
        return view('firewall.dashboard', compact('firewall'));
    }
}
