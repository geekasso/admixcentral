<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;

class DiagnosticsController extends Controller
{
    public function arpTable(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $arpTable = [];
        try {
            $arpTable = $api->getArpTable()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error or handle gracefully
        }
        return view('diagnostics.arp-table', compact('firewall', 'arpTable'));
    }

    public function authentication(Firewall $firewall)
    {
        return view('diagnostics.authentication', compact('firewall'));
    }

    public function backupRestore(Firewall $firewall)
    {
        return view('diagnostics.backup-restore', compact('firewall'));
    }

    public function commandPrompt(Request $request, Firewall $firewall)
    {
        $output = null;
        if ($request->isMethod('post')) {
            $request->validate(['command' => 'required|string']);
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $response = $api->diagnosticsCommandPrompt($request->input('command'));
                $output = $response['data'] ?? [];
            } catch (\Exception $e) {
                $output = ['error' => $e->getMessage()];
            }
        }
        return view('diagnostics.command-prompt', compact('firewall', 'output'));
    }

    public function dnsLookup(Request $request, Firewall $firewall)
    {
        $output = null;
        if ($request->isMethod('post')) {
            $request->validate(['host' => 'required|string']);
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $host = escapeshellarg($request->input('host'));
                $response = $api->commandPrompt("host " . $host);
                $output = $response['data']['output'] ?? [];
            } catch (\Exception $e) {
                $output = ['error' => $e->getMessage()];
            }
        }
        return view('diagnostics.dns-lookup', compact('firewall', 'output'));
    }

    public function editFile(Firewall $firewall)
    {
        return view('diagnostics.edit-file', compact('firewall'));
    }

    public function factoryDefaults(Firewall $firewall)
    {
        return view('diagnostics.factory-defaults', compact('firewall'));
    }

    public function haltSystem(Request $request, Firewall $firewall)
    {
        if ($request->isMethod('post')) {
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $api->diagnosticsHalt();
                return back()->with('success', 'System halt initiated.');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to halt system: ' . $e->getMessage());
            }
        }
        return view('diagnostics.halt-system', compact('firewall'));
    }

    public function limiterInfo(Firewall $firewall)
    {
        return view('diagnostics.limiter-info', compact('firewall'));
    }

    public function ndpTable(Firewall $firewall)
    {
        return view('diagnostics.ndp-table', compact('firewall'));
    }

    public function packetCapture(Firewall $firewall)
    {
        return view('diagnostics.packet-capture', compact('firewall'));
    }

    public function pfInfo(Firewall $firewall)
    {
        return view('diagnostics.pf-info', compact('firewall'));
    }

    public function pfTop(Firewall $firewall)
    {
        return view('diagnostics.pf-top', compact('firewall'));
    }

    public function ping(Request $request, Firewall $firewall)
    {
        $output = null;
        if ($request->isMethod('post')) {
            $request->validate([
                'host' => 'required|string',
                'count' => 'nullable|integer|min:1|max:10',
                'interface' => 'nullable|string',
            ]);
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $host = escapeshellarg($request->input('host'));
                $count = (int) $request->input('count', 3);
                $interface = $request->input('interface', 'wan');

                // If interface is a specific IP or name, we might pass -S. For now simple ping.
                $command = "ping -c {$count} " . $host;

                $response = $api->commandPrompt($command);
                $output = $response['data']['output'] ?? [];
            } catch (\Exception $e) {
                $output = ['error' => $e->getMessage()];
            }
        }
        return view('diagnostics.ping', compact('firewall', 'output'));
    }

    public function reboot(Request $request, Firewall $firewall)
    {
        if ($request->isMethod('post')) {
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $api->diagnosticsReboot();
                return back()->with('success', 'System reboot initiated.');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to reboot system: ' . $e->getMessage());
            }
        }
        return view('diagnostics.reboot', compact('firewall'));
    }

    public function routes(Firewall $firewall)
    {
        return view('diagnostics.routes', compact('firewall'));
    }

    public function smartStatus(Firewall $firewall)
    {
        return view('diagnostics.smart-status', compact('firewall'));
    }

    public function sockets(Firewall $firewall)
    {
        return view('diagnostics.sockets', compact('firewall'));
    }

    public function states(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $states = [];
        try {
            $states = $api->getFirewallStates()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }
        return view('diagnostics.states', compact('firewall', 'states'));
    }

    public function statesSummary(Firewall $firewall)
    {
        return view('diagnostics.states-summary', compact('firewall'));
    }

    public function systemActivity(Firewall $firewall)
    {
        return view('diagnostics.system-activity', compact('firewall'));
    }

    public function tables(Request $request, Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $tables = [];
        $tableContent = [];
        $selectedTable = $request->input('table');

        try {
            $tables = $api->getDiagnosticsTables()['data'] ?? [];
            if ($selectedTable) {
                $tableContent = $api->getDiagnosticsTable($selectedTable)['data'] ?? [];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Diagnostics Tables Error: ' . $e->getMessage());
        }
        return view('diagnostics.tables', compact('firewall', 'tables', 'tableContent', 'selectedTable'));
    }

    public function testPort(Firewall $firewall)
    {
        return view('diagnostics.test-port', compact('firewall'));
    }

    public function traceroute(Request $request, Firewall $firewall)
    {
        $output = null;
        if ($request->isMethod('post')) {
            $request->validate(['host' => 'required|string']);
            $api = new \App\Services\PfSenseApiService($firewall);
            try {
                $host = escapeshellarg($request->input('host'));
                $command = "traceroute -w 2 -m 15 " . $host;

                $response = $api->commandPrompt($command);
                $output = $response['data']['output'] ?? [];
            } catch (\Exception $e) {
                $output = ['error' => $e->getMessage()];
            }
        }
        return view('diagnostics.traceroute', compact('firewall', 'output'));
    }
}
