<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class VpnIpsecController extends Controller
{
    public function tunnels(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->get('/vpn/ipsec/phase1');

            $phase1 = $response['data'] ?? [];

            return view('vpn.ipsec.tunnels', compact('firewall', 'phase1'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch IPsec tunnels: ' . $e->getMessage());
        }
    }

    public function createPhase1(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $interfacesResponse = $api->get('/interface');
            $interfaces = $interfacesResponse['data'] ?? [];

            $tunnel = [
                'iketype' => 'ikev2',
                'interface' => 'wan',
                'remote-gateway' => '',
                'descr' => '',
                'authentication_method' => 'pre_shared_key',
                'myid_type' => 'myaddress',
                'peerid_type' => 'peeraddress',
                'pre-shared-key' => '',
                'encryption' => ['aes'],
                'hash' => ['sha256'],
                'dhgroup' => ['14'],
                'lifetime' => 28800,
            ];

            return view('vpn.ipsec.edit-phase1', compact('firewall', 'tunnel', 'interfaces'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to prepare IPsec tunnel creation: ' . $e->getMessage());
        }
    }

    public function storePhase1(Request $request, Firewall $firewall)
    {
        $validated = $request->validate([
            'iketype' => 'required|in:ikev1,ikev2,auto',
            'interface' => 'required|string',
            'remote_gateway' => 'required|string',
            'descr' => 'nullable|string',
            'authentication_method' => 'required|in:pre_shared_key,rsasig',
            'pre_shared_key' => 'required_if:authentication_method,pre_shared_key',
            'myid_type' => 'required|string',
            'peerid_type' => 'required|string',
            'lifetime' => 'nullable|integer|min:60',
        ]);

        $data = [
            'iketype' => $validated['iketype'],
            'interface' => $validated['interface'],
            'remote-gateway' => $validated['remote_gateway'],
            'descr' => $validated['descr'] ?? '',
            'authentication_method' => $validated['authentication_method'],
            'myid_type' => $validated['myid_type'],
            'peerid_type' => $validated['peerid_type'],
            'lifetime' => $validated['lifetime'] ?? 28800,
        ];

        if ($validated['authentication_method'] === 'pre_shared_key') {
            $data['pre-shared-key'] = $validated['pre_shared_key'];
        }

        try {
            $api = new PfSenseApiService($firewall);
            $api->post('/vpn/ipsec/phase1', $data);

            return redirect()->route('vpn.ipsec.tunnels', $firewall)
                ->with('success', 'IPsec tunnel created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create IPsec tunnel: ' . $e->getMessage());
        }
    }

    public function phase2(Firewall $firewall, string $phase1Id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->get('/vpn/ipsec/phase2');

            $phase2List = collect($response['data'] ?? [])->where('ikeid', $phase1Id);

            return view('vpn.ipsec.phase2', compact('firewall', 'phase1Id', 'phase2List'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch Phase 2 entries: ' . $e->getMessage());
        }
    }
}
