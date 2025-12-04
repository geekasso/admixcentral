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
        if ($user->role === 'admin') {
            $firewalls = Firewall::with('company')->get();
        } else {
            $firewalls = Firewall::where('company_id', $user->company_id)->get();
        }

        // Collect status for each firewall
        $firewallsWithStatus = $firewalls->map(function ($firewall) {
            try {
                $api = new PfSenseApiService($firewall);
                $status = $api->getSystemStatus();
                $firewall->status = $status;
                $firewall->online = true;
            } catch (\Exception $e) {
                $firewall->status = null;
                $firewall->online = false;
                $firewall->error = $e->getMessage();
            }
            return $firewall;
        });

        return view('dashboard', compact('firewallsWithStatus'));
    }

    public function firewall(Request $request, Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $systemStatus = [];
        $interfaces = [];
        $firewallRules = [];
        $apiError = null;

        try {
            $systemStatus = $api->getSystemStatus();
            $systemStatus['connected'] = true;

            // Try to get version info
            try {
                $versionInfo = $api->getSystemVersion();
                if (isset($versionInfo['data'])) {
                    $systemStatus['data'] = array_merge($systemStatus['data'] ?? [], $versionInfo['data']);
                }
            } catch (\Exception $e) {
                // Ignore version fetch error
            }
        } catch (\Exception $e) {
            $apiError = "System Status: " . $e->getMessage();
            $systemStatus['connected'] = false;
        }

        try {
            $interfacesResponse = $api->getInterfacesStatus();
            $interfacesStatusData = $interfacesResponse['data'] ?? [];

            // Get interfaces config (to check enabled state)
            $interfacesConfigResponse = $api->getInterfaces();
            $interfacesConfigData = $interfacesConfigResponse['data'] ?? [];

            // Merge enabled status from config into status data
            $interfaces = ['data' => []];
            foreach ($interfacesStatusData as $statusItem) {
                $configItem = collect($interfacesConfigData)->firstWhere('id', $statusItem['name']);
                if ($configItem) {
                    $statusItem['enable'] = $configItem['enable'] ?? false;
                }
                $interfaces['data'][] = $statusItem;
            }
        } catch (\Exception $e) {
            $apiError .= " | Interfaces: " . $e->getMessage();
        }

        try {
            $gatewaysResponse = $api->getGateways();
            $gateways = $gatewaysResponse['data'] ?? [];
        } catch (\Exception $e) {
            $apiError .= " | Gateways: " . $e->getMessage();
        }

        try {
            $firewallRules = $api->getFirewallRules();
        } catch (\Exception $e) {
            $apiError .= " | Rules: " . $e->getMessage();
        }

        return view('firewall.dashboard', compact('firewall', 'systemStatus', 'interfaces', 'gateways', 'firewallRules', 'apiError'));
    }
}
