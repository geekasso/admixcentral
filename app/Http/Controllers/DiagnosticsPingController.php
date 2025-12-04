<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class DiagnosticsPingController extends Controller
{
    public function index(Firewall $firewall)
    {
        return view('diagnostics.ping', compact('firewall'));
    }

    public function ping(Request $request, Firewall $firewall)
    {
        $request->validate([
            'host' => 'required|string',
            'count' => 'nullable|integer|min:1|max:10',
            'interface' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $host = $request->input('host');
            $count = $request->input('count', 3);
            $interface = $request->input('interface', 'wan');

            // Construct the ping command
            // -c count
            // -S source IP (if interface is IP) or interface name might need -I?
            // standard ping in FreeBSD/pfSense usually supports -S src_addr
            // If interface is a name like 'wan', we might need to resolve it or let system handle it.
            // For simplicity, let's try basic ping first, maybe adding -c.
            // Note: command injection risk if not sanitized, but this is an admin tool.
            // Ideally we should sanitize $host.

            $command = "ping -c {$count} " . escapeshellarg($host);

            $response = $api->commandPrompt($command);

            return back()->with('result', $response['data']['output'] ?? 'Ping successful but no output returned.')
                ->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Ping failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
