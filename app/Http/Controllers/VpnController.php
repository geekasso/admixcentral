<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;

class VpnController extends Controller
{
    public function ipsec(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $phase1s = [];
        $phase2s = [];

        try {
            $phase1s = $api->getIpsecPhase1s()['data'] ?? [];
            $phase2s = $api->getIpsecPhase2s()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error or handle it
        }

        return view('vpn.ipsec', compact('firewall', 'phase1s', 'phase2s'));
    }

    public function l2tp(Firewall $firewall)
    {
        return view('vpn.l2tp', compact('firewall'));
    }
}
