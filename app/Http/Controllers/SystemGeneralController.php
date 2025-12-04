<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class SystemGeneralController extends Controller
{
    public function index(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->get('/system/config');

            $config = $response['data'] ?? [];

            return view('system.general', compact('firewall', 'config'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch system configuration: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Firewall $firewall)
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'dns1' => 'nullable|ip',
            'dns2' => 'nullable|ip',
            'dns3' => 'nullable|ip',
            'dns4' => 'nullable|ip',
            'timezone' => 'required|string',
        ]);

        $data = [
            'hostname' => $validated['hostname'],
            'domain' => $validated['domain'],
            'dnsserver' => array_filter([
                $validated['dns1'] ?? null,
                $validated['dns2'] ?? null,
                $validated['dns3'] ?? null,
                $validated['dns4'] ?? null,
            ]),
            'timezone' => $validated['timezone'],
        ];

        try {
            $api = new PfSenseApiService($firewall);
            $api->put('/system/config', $data);

            return back()->with('success', 'System configuration updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update system configuration: ' . $e->getMessage());
        }
    }
}
