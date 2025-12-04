<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class FirewallApplyController extends Controller
{
    public function apply(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->applyChanges();

            $firewall->update(['is_dirty' => false]);

            return back()->with('success', 'Changes applied successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to apply changes: ' . $e->getMessage());
        }
    }
}
