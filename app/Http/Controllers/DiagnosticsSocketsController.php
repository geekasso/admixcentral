<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class DiagnosticsSocketsController extends Controller
{
    public function index(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $sockets = null;
        $error = null;

        try {
            $response = $api->getSockets();
            $sockets = $response['data'] ?? [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('diagnostics.sockets.index', compact('firewall', 'sockets', 'error'));
    }
}
