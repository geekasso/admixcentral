<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class DiagnosticsSmartStatusController extends Controller
{
    public function index(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $smartStatus = null;
        $error = null;

        try {
            $response = $api->getSmartStatus();
            $smartStatus = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('diagnostics.smart_status.index', compact('firewall', 'smartStatus', 'error'));
    }
}
