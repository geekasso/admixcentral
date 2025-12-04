<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function captivePortal(Firewall $firewall)
    {
        return view('services.captive-portal', compact('firewall'));
    }

    public function dhcpRelay(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $config = [];

        try {
            $config = $api->getDhcpRelay()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }

        return view('services.dhcp-relay', compact('firewall', 'config'));
    }

    public function updateDhcpRelay(Request $request, Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);

        try {
            $api->updateDhcpRelay($request->all());
            return redirect()->back()->with('status', 'DHCP Relay settings updated.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function dhcpv6Relay(Firewall $firewall)
    {
        return view('services.dhcpv6-relay', compact('firewall'));
    }

    public function dhcpv6Server(Firewall $firewall)
    {
        return view('services.dhcpv6-server', compact('firewall'));
    }

    public function dnsForwarder(Firewall $firewall)
    {
        return view('services.dns-forwarder', compact('firewall'));
    }

    public function dynamicDns(Firewall $firewall)
    {
        return view('services.dynamic-dns', compact('firewall'));
    }

    public function igmpProxy(Firewall $firewall)
    {
        return view('services.igmp-proxy', compact('firewall'));
    }

    public function ntp(Firewall $firewall)
    {
        return view('services.ntp', compact('firewall'));
    }

    public function pppoeServer(Firewall $firewall)
    {
        return view('services.pppoe-server', compact('firewall'));
    }

    public function routerAdvertisement(Firewall $firewall)
    {
        return view('services.router-advertisement', compact('firewall'));
    }

    public function snmp(Firewall $firewall)
    {
        return view('services.snmp', compact('firewall'));
    }

    public function upnp(Firewall $firewall)
    {
        return view('services.upnp', compact('firewall'));
    }

    public function wakeOnLan(Firewall $firewall)
    {
        return view('services.wake-on-lan', compact('firewall'));
    }
}
