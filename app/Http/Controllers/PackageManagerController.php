<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class PackageManagerController extends Controller
{
    public function index(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $tab = $request->query('tab', 'installed');

        $data = [];
        try {
            if ($tab === 'installed') {
                $data['packages'] = $api->getSystemPackages()['data'] ?? [];
            } elseif ($tab === 'available') {
                $data['packages'] = $api->getSystemAvailablePackages()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Handle API errors gracefully, maybe the endpoint is slow or times out
            $data['error'] = $e->getMessage();
            $data['packages'] = [];
        }

        return view('system.package_manager.index', compact('firewall', 'tab', 'data'));
    }

    public function install(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $name = $request->input('name');

        try {
            $api->installSystemPackage($name);
            return redirect()->route('system.package_manager.index', ['firewall' => $firewall, 'tab' => 'installed'])
                ->with('success', "Package '$name' installation started. It may take a few minutes to appear.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Failed to install package '$name': " . $e->getMessage()]);
        }
    }

    public function uninstall(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $name = $request->input('name');

        try {
            $api->uninstallSystemPackage($name);
            return redirect()->route('system.package_manager.index', ['firewall' => $firewall, 'tab' => 'installed'])
                ->with('success', "Package '$name' uninstallation started.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Failed to uninstall package '$name': " . $e->getMessage()]);
        }
    }
}
