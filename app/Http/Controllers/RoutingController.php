<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoutingController extends Controller
{
    public function index(Firewall $firewall, Request $request)
    {
        $tab = $request->query('tab', 'gateways');
        $api = new PfSenseApiService($firewall);
        $data = [];

        try {
            switch ($tab) {
                case 'gateways':
                    $data['gateways'] = $api->getRoutingGateways()['data'] ?? [];
                    break;
                case 'static_routes':
                    $data['static_routes'] = $api->getRoutingStaticRoutes()['data'] ?? [];
                    break;
                case 'gateway_groups':
                    $data['gateway_groups'] = $api->getRoutingGatewayGroups()['data'] ?? [];
                    break;
            }
        } catch (\Exception $e) {
            // Handle API errors gracefully, maybe flash a message
            session()->flash('error', 'Failed to fetch data: ' . $e->getMessage());
        }

        return view('system.routing', compact('firewall', 'tab', 'data'));
    }

    // Gateways
    public function storeGateway(Request $request, Firewall $firewall)
    {
        $request->validate([
            'name' => 'required|string',
            'interface' => 'required|string',
            'ipprotocol' => 'required|string',
            'gateway' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $api->createRoutingGateway($request->all());
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateways'])
                ->with('success', 'Gateway created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create gateway: ' . $e->getMessage()]);
        }
    }

    public function updateGateway(Request $request, Firewall $firewall, string $id)
    {
        // Validation can be similar to store, but maybe some fields are read-only or optional
        $request->validate([
            'name' => 'required|string',
            'interface' => 'required|string',
            'ipprotocol' => 'required|string',
            'gateway' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $data = $request->all();
            $data['id'] = $id; // Ensure ID is passed if API expects it in body
            $api->updateRoutingGateway($data);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateways'])
                ->with('success', 'Gateway updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update gateway: ' . $e->getMessage()]);
        }
    }

    public function destroyGateway(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->deleteRoutingGateway($id);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateways'])
                ->with('success', 'Gateway deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete gateway: ' . $e->getMessage()]);
        }
    }

    // Static Routes
    public function storeStaticRoute(Request $request, Firewall $firewall)
    {
        $request->validate([
            'network' => 'required|string',
            'gateway' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $api->createRoutingStaticRoute($request->all());
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'static_routes'])
                ->with('success', 'Static route created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create static route: ' . $e->getMessage()]);
        }
    }

    public function updateStaticRoute(Request $request, Firewall $firewall, string $id)
    {
        $request->validate([
            'network' => 'required|string',
            'gateway' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $data = $request->all();
            $data['id'] = $id;
            $api->updateRoutingStaticRoute($data);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'static_routes'])
                ->with('success', 'Static route updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update static route: ' . $e->getMessage()]);
        }
    }

    public function destroyStaticRoute(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->deleteRoutingStaticRoute($id);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'static_routes'])
                ->with('success', 'Static route deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete static route: ' . $e->getMessage()]);
        }
    }

    // Gateway Groups
    public function storeGatewayGroup(Request $request, Firewall $firewall)
    {
        $request->validate([
            'name' => 'required|string',
            'item' => 'required|array', // Items are usually array of gateway configs
            'trigger' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $api->createRoutingGatewayGroup($request->all());
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateway_groups'])
                ->with('success', 'Gateway group created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create gateway group: ' . $e->getMessage()]);
        }
    }

    public function updateGatewayGroup(Request $request, Firewall $firewall, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'item' => 'required|array',
            'trigger' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $data = $request->all();
            $data['id'] = $id;
            $api->updateRoutingGatewayGroup($data);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateway_groups'])
                ->with('success', 'Gateway group updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update gateway group: ' . $e->getMessage()]);
        }
    }

    public function destroyGatewayGroup(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->deleteRoutingGatewayGroup($id);
            $firewall->update(['is_dirty' => true]);
            return redirect()->route('firewall.system.routing', ['firewall' => $firewall->id, 'tab' => 'gateway_groups'])
                ->with('success', 'Gateway group deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete gateway group: ' . $e->getMessage()]);
        }
    }
}
