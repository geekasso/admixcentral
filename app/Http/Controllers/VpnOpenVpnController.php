<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VpnOpenVpnController extends Controller
{
    public function servers(\App\Models\Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $servers = [];
        try {
            $servers = $api->getOpenVpnServers()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error or handle it
        }
        return view('vpn.openvpn.servers', compact('firewall', 'servers'));
    }

    public function clients(\App\Models\Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $clients = [];
        try {
            $clients = $api->getOpenVpnClients()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error or handle it
        }
        return view('vpn.openvpn.clients', compact('firewall', 'clients'));
    }

    public function createServer(\App\Models\Firewall $firewall)
    {
        try {
            $api = new \App\Services\PfSenseApiService($firewall);
            $interfaces = $api->getInterfaces()['data'] ?? [];
            $cas = $api->getCertificateAuthorities()['data'] ?? [];
            $certs = $api->getCertificates()['data'] ?? [];

            return view('vpn.openvpn.edit-server', compact('firewall', 'interfaces', 'cas', 'certs'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load OpenVPN server creation form: ' . $e->getMessage());
        }
    }

    public function storeServer(Request $request, \App\Models\Firewall $firewall)
    {
        // Placeholder
        return redirect()->route('vpn.openvpn.servers', $firewall);
    }
}
