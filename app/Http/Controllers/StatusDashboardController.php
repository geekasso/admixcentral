<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;

class StatusDashboardController extends Controller
{
    public function index(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);

            // Get system info
            $systemStatus = $api->getSystemStatus();
            $systemStatus['connected'] = true;
            $system = $systemStatus['data'] ?? [];

            // Get interfaces status (runtime)
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

            // Get gateways status
            $gatewaysResponse = $api->getGateways();
            $gateways = $gatewaysResponse['data'] ?? [];

            // Get services status
            $services = [];
            return view('status.dashboard', compact('firewall', 'system', 'interfaces', 'gateways', 'services', 'systemStatus'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch status information: ' . $e->getMessage());
        }
    }
}
