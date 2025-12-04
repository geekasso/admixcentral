<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirewallAliasController extends Controller
{
    public function index(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->get('/firewall/aliases');

            $aliases = $response['data'] ?? [];

            return view('firewall.aliases.index', compact('firewall', 'aliases'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch aliases: ' . $e->getMessage());
            return back()->with('error', 'Failed to fetch aliases: ' . $e->getMessage());
        }
    }

    public function create(Firewall $firewall)
    {
        $alias = [
            'name' => '',
            'type' => 'host',
            '

descr' => '',
            'address' => [''],
            'detail' => [''],
        ];

        return view('firewall.aliases.edit', compact('firewall', 'alias'));
    }

    public function store(Request $request, Firewall $firewall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'type' => 'required|in:host,network,port,url,urltable',
            'descr' => 'nullable|string',
            'address' => 'required|array',
            'address.*' => 'required|string',
            'detail' => 'array',
            'detail.*' => 'nullable|string',
        ]);

        // Prepare data for API
        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'descr' => $validated['descr'] ?? '',
            'address' => array_values(array_filter($validated['address'])),
            'detail' => array_values(array_map(fn($d) => $d ?? '', $validated['detail'])),
        ];

        try {
            $api = new PfSenseApiService($firewall);
            $api->post('/firewall/alias', $data);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('firewall.aliases.index', $firewall)
                ->with('success', 'Alias created successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create alias: ' . $e->getMessage());
        }
    }

    public function edit(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->get('/firewall/alias');
            $aliases = $response['data'] ?? [];

            // Find alias by ID
            $alias = collect($aliases)->firstWhere('id', $id);

            if (!$alias) {
                return back()->with('error', 'Alias not found.');
            }

            // Parse address and detail fields
            $alias['address'] = !empty($alias['address']) ? explode(' ', $alias['address']) : [''];
            $alias['detail'] = !empty($alias['detail']) ? explode('||', $alias['detail']) : [''];

            // Ensure arrays have same length
            $alias['address'] = array_pad($alias['address'], max(count($alias['address']), 1), '');
            $alias['detail'] = array_pad($alias['detail'], max(count($alias['detail']), 1), '');

            return view('firewall.aliases.edit', compact('firewall', 'alias'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch alias: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Firewall $firewall, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'type' => 'required|in:host,network,port,url,urltable',
            'descr' => 'nullable|string',
            'address' => 'required|array',
            'address.*' => 'required|string',
            'detail' => 'array',
            'detail.*' => 'nullable|string',
        ]);

        $data = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'descr' => $validated['descr'] ?? '',
            'address' => implode(' ', array_filter($validated['address'])),
            'detail' => implode('||', array_map(fn($d) => $d ?? '', $validated['detail'])),
        ];

        try {
            $api = new PfSenseApiService($firewall);
            $api->put('/firewall/alias/' . $id, $data);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('firewall.aliases.index', $firewall)
                ->with('success', 'Alias updated successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update alias: ' . $e->getMessage());
        }
    }

    public function destroy(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->delete('/firewall/alias', ['id' => $id]);
            $firewall->update(['is_dirty' => true]);

            return back()->with('success', 'Alias deleted successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete alias: ' . $e->getMessage());
        }
    }


}
