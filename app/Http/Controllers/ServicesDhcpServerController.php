<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class ServicesDhcpServerController extends Controller
{
    public function index(Firewall $firewall, $interface = 'lan')
    {
        try {
            $api = new PfSenseApiService($firewall);

            // Get interfaces
            $interfacesResponse = $api->get('/interfaces');
            $interfaces = $interfacesResponse['data'] ?? [];
            // $interfaces = [];

            // Get DHCP configuration for specific interface
            $dhcpResponse = $api->getDhcpServer($interface);
            $selectedConfig = $dhcpResponse['data'] ?? [];

            return view('services.dhcp.index', compact('firewall', 'interfaces', 'selectedConfig', 'interface'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch DHCP configuration: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Firewall $firewall, $interface)
    {
        $validated = $request->validate([
            'enable' => 'boolean',
            'range_from' => 'nullable|ip',
            'range_to' => 'nullable|ip',
            'domain' => 'nullable|string',
            'defaultleasetime' => 'nullable|integer|min:60',
            'maxleasetime' => 'nullable|integer|min:60',
            'dns1' => 'nullable|ip',
            'dns2' => 'nullable|ip',
            'dns3' => 'nullable|ip',
            'dns4' => 'nullable|ip',
            'gateway' => 'nullable|ip',
            'wins1' => 'nullable|ip',
            'wins2' => 'nullable|ip',
            'ntp1' => 'nullable|ip',
            'ntp2' => 'nullable|ip',
            'tftp' => 'nullable|string',
            'netboot_server_ip' => 'nullable|ip',
            'next_server' => 'nullable|ip',
        ]);

        $data = [
            'id' => $interface, // Required by API
            'interface' => $interface,
            'enable' => $request->has('enable'),
            'range' => [
                'from' => $validated['range_from'] ?? '',
                'to' => $validated['range_to'] ?? '',
            ],
            'domain' => $validated['domain'] ?? '',
            'defaultleasetime' => $validated['defaultleasetime'] ?? 7200,
            'maxleasetime' => $validated['maxleasetime'] ?? 86400,
            'dnsserver' => array_filter([
                $validated['dns1'] ?? null,
                $validated['dns2'] ?? null,
                $validated['dns3'] ?? null,
                $validated['dns4'] ?? null,
            ]),
            'gateway' => $validated['gateway'] ?? '',
            'winsserver' => array_filter([
                $validated['wins1'] ?? null,
                $validated['wins2'] ?? null,
            ]),
            'ntpserver' => array_filter([
                $validated['ntp1'] ?? null,
                $validated['ntp2'] ?? null,
            ]),
            'tftp' => $validated['tftp'] ?? '',
            'nextserver' => $validated['next_server'] ?? '',
        ];

        try {
            $api = new PfSenseApiService($firewall);
            $api->updateDhcpServer($data);

            return back()->with('success', 'DHCP Server configuration updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update DHCP configuration: ' . $e->getMessage());
        }
    }
}
