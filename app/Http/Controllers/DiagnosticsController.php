<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use Illuminate\Http\Request;

class DiagnosticsController extends Controller
{
    public function arpTable(Firewall $firewall)
    {
        return view('diagnostics.arp-table', compact('firewall'));
    }

    public function authentication(Firewall $firewall)
    {
        return view('diagnostics.authentication', compact('firewall'));
    }

    public function backupRestore(Firewall $firewall)
    {
        return view('diagnostics.backup-restore', compact('firewall'));
    }

    public function commandPrompt(Firewall $firewall)
    {
        return view('diagnostics.command-prompt', compact('firewall'));
    }

    public function dnsLookup(Firewall $firewall)
    {
        return view('diagnostics.dns-lookup', compact('firewall'));
    }

    public function editFile(Firewall $firewall)
    {
        return view('diagnostics.edit-file', compact('firewall'));
    }

    public function factoryDefaults(Firewall $firewall)
    {
        return view('diagnostics.factory-defaults', compact('firewall'));
    }

    public function haltSystem(Firewall $firewall)
    {
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

    public function ping(Firewall $firewall)
    {
        return view('diagnostics.ping', compact('firewall'));
    }

    public function reboot(Firewall $firewall)
    {
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
        return view('diagnostics.states', compact('firewall'));
    }

    public function statesSummary(Firewall $firewall)
    {
        return view('diagnostics.states-summary', compact('firewall'));
    }

    public function systemActivity(Firewall $firewall)
    {
        return view('diagnostics.system-activity', compact('firewall'));
    }

    public function tables(Firewall $firewall)
    {
        return view('diagnostics.tables', compact('firewall'));
    }

    public function testPort(Firewall $firewall)
    {
        return view('diagnostics.test-port', compact('firewall'));
    }

    public function traceroute(Firewall $firewall)
    {
        return view('diagnostics.traceroute', compact('firewall'));
    }
}
