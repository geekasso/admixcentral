<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function captivePortal(Firewall $firewall)
    {
        return view('status.captive-portal', compact('firewall'));
    }

    public function carp(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $carpStatus = [];
        $virtualIps = [];

        try {
            $carpStatus = $api->getCarpStatus()['data'] ?? [];
            $virtualIps = $api->getVirtualIps()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }

        return view('status.carp', compact('firewall', 'carpStatus', 'virtualIps'));
    }

    public function updateCarp(Request $request, Firewall $firewall)
    {
        try {
            $api = new \App\Services\PfSenseApiService($firewall);
            $data = [
                'enable' => $request->has('enable'),
                'maintenance_mode' => $request->has('maintenance_mode'),
            ];
            $api->updateCarpStatus($data);
            return back()->with('success', 'CARP status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update CARP status: ' . $e->getMessage());
        }
    }

    public function dhcpLeases(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $leases = [];
        try {
            $leases = $api->getDhcpLeases()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.dhcp-leases', compact('firewall', 'leases'));
    }

    public function dhcpv6Leases(Firewall $firewall)
    {
        return view('status.dhcpv6-leases', compact('firewall'));
    }

    public function filterReload(Firewall $firewall)
    {
        return view('status.filter-reload', compact('firewall'));
    }



    public function ipsec(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $status = [];
        try {
            $status = $api->getIpsecStatus()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.ipsec', compact('firewall', 'status'));
    }

    public function monitoring(Firewall $firewall)
    {
        return view('status.monitoring', compact('firewall'));
    }

    public function ntp(Firewall $firewall)
    {
        return view('status.ntp', compact('firewall'));
    }

    public function openvpn(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $status = [];
        try {
            $status = $api->getOpenVpnServerStatus()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.openvpn', compact('firewall', 'status'));
    }

    public function dhcp(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $leases = [];
        try {
            $leases = $api->getDhcpLeases()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.dhcp', compact('firewall', 'leases'));
    }

    public function gateways(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $gateways = [];
        try {
            $gateways = $api->getGateways()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.gateways', compact('firewall', 'gateways'));
    }

    public function interfaces(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $interfaces = [];
        try {
            $interfaces = $api->getInterfacesStatus()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.interfaces', compact('firewall', 'interfaces'));
    }

    public function services(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $services = [];
        try {
            $services = $api->getServicesStatus()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.services', compact('firewall', 'services'));
    }

    public function system(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $system = [];
        try {
            $system = $api->getSystemStatus()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.system', compact('firewall', 'system'));
    }

    public function queues(Firewall $firewall)
    {
        return view('status.queues', compact('firewall'));
    }


    public function systemLogs(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $logs = [];
        try {
            // Default to system logs
            $logs = $api->getSystemLogs('system')['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('status.system-logs', compact('firewall', 'logs'));
    }

    public function trafficGraph(Firewall $firewall)
    {
        return view('status.traffic-graph', compact('firewall'));
    }

    public function upnp(Firewall $firewall)
    {
        return view('status.upnp', compact('firewall'));
    }
}
