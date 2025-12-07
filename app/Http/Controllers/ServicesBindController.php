<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Log;

class ServicesBindController extends Controller
{
    public function index(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $zones = [];
        $error = null;

        try {
            $response = $api->getBindZones();
            $zones = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
            // Fallback: If API returns 404, we assume endpoint doesn't exist.
            if (str_contains($e->getMessage(), '404')) {
                return view('services.bind.index', compact('firewall', 'zones'))->with('api_not_supported', true);
            }
        }

        return view('services.bind.index', compact('firewall', 'zones', 'error'));
    }

    public function settings(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $settings = [];
        $error = null;

        try {
            $response = $api->getBindSettings();
            $settings = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
            if (str_contains($e->getMessage(), '404')) {
                return view('services.bind.settings', compact('firewall', 'settings'))->with('api_not_supported', true);
            }
        }

        return view('services.bind.settings', compact('firewall', 'settings', 'error'));
    }
}
