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
        return view('status.carp', compact('firewall'));
    }

    public function dhcpLeases(Firewall $firewall)
    {
        return view('status.dhcp-leases', compact('firewall'));
    }

    public function dhcpv6Leases(Firewall $firewall)
    {
        return view('status.dhcpv6-leases', compact('firewall'));
    }

    public function filterReload(Firewall $firewall)
    {
        return view('status.filter-reload', compact('firewall'));
    }

    public function gateways(Firewall $firewall)
    {
        return view('status.gateways', compact('firewall'));
    }

    public function interfaces(Firewall $firewall)
    {
        return view('status.interfaces', compact('firewall'));
    }

    public function ipsec(Firewall $firewall)
    {
        return view('status.ipsec', compact('firewall'));
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
        return view('status.openvpn', compact('firewall'));
    }

    public function queues(Firewall $firewall)
    {
        return view('status.queues', compact('firewall'));
    }

    public function services(Firewall $firewall)
    {
        return view('status.services', compact('firewall'));
    }

    public function systemLogs(Firewall $firewall)
    {
        return view('status.system-logs', compact('firewall'));
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
