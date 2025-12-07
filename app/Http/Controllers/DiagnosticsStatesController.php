<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class DiagnosticsStatesController extends Controller
{
    public function index(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $states = null;
        $error = null;

        try {
            $response = $api->getFirewallStates();
            $states = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('diagnostics.states.index', compact('firewall', 'states', 'error'));
    }

    public function summary(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $summary = null;
        $error = null;

        try {
            $response = $api->getStatesSummary();
            $summary = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('diagnostics.states_summary.index', compact('firewall', 'summary', 'error'));
    }
}
