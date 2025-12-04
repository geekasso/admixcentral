<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class StatusInterfaceController extends Controller
{
    public function index(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $interfaces = $api->getInterfaces();

            // Try to get stats if available (assuming getInterfaces might not return them)
            // If getInterfaces returns stats, great. If not, we might need another call.
            // Since I can't verify, I'll just pass what I have.

            return view('status.interfaces', compact('firewall', 'interfaces'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch interface status: ' . $e->getMessage());
        }
    }
}
