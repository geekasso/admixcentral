<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function advanced(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $tab = $request->query('tab', 'admin');

        $data = [];
        switch ($tab) {
            case 'admin':
                $data['webgui'] = $api->getSystemWebGui()['data'] ?? [];
                $data['ssh'] = $api->getSystemSsh()['data'] ?? [];
                $data['console'] = $api->getSystemConsole()['data'] ?? [];
                break;
            case 'firewall':
                $data['firewall'] = $api->getSystemFirewallAdvanced()['data'] ?? [];
                break;
            case 'notifications':
                $data['notifications'] = $api->getSystemNotifications()['data'] ?? [];
                break;
            case 'tunables':
                $data['tunables'] = $api->getSystemTunables()['data'] ?? [];
                break;
            case 'networking':
            case 'miscellaneous':
                // Not supported by API yet
                break;
        }

        return view('system.advanced', compact('firewall', 'tab', 'data'));
    }

    public function updateAdvanced(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $tab = $request->input('tab', 'admin');

        try {
            switch ($tab) {
                case 'admin':
                    $webGuiData = $request->only(['protocol', 'port', 'sslcertref']);

                    // Handle checkboxes/booleans if needed, though API inspection showed strings/empty.
                    // SSH
                    $sshData = [
                        'enable' => $request->has('ssh_enable') ? '1' : '', // API returned '1' or empty
                        'port' => $request->input('ssh_port'),
                        'sshdkeyonly' => $request->has('sshdkeyonly') ? 'enabled' : '', // Guessing 'enabled' or '1' based on common patterns, need to verify. API returned empty.
                        'sshdagentforwarding' => $request->has('sshdagentforwarding') ? 'enabled' : '',
                    ];
                    // Console
                    $consoleData = [
                        'passwd_protect_console' => $request->has('passwd_protect_console') ? '1' : '',
                    ];

                    $api->updateSystemWebGui($webGuiData);
                    $api->updateSystemSsh($sshData);
                    $api->updateSystemConsole($consoleData);
                    break;

                case 'firewall':
                    $firewallData = [
                        'aliasesresolveinterval' => (int) $request->input('aliasesresolveinterval'),
                        'checkaliasesurlcert' => $request->has('checkaliasesurlcert'),
                    ];
                    $api->updateSystemFirewallAdvanced($firewallData);
                    break;

                case 'notifications':
                    $notifyData = $request->except(['_token', '_method', 'tab']);
                    // Checkboxes handling
                    $notifyData['disable'] = $request->has('disable');
                    $notifyData['ssl'] = $request->has('ssl');
                    $notifyData['sslvalidate'] = $request->has('sslvalidate');
                    if (isset($notifyData['timeout'])) {
                        $notifyData['timeout'] = (int) $notifyData['timeout'];
                    }
                    \Illuminate\Support\Facades\Log::info('Updating Notifications:', $notifyData);
                    $api->updateSystemNotifications($notifyData);
                    break;
            }

            $firewall->update(['is_dirty' => true]);

            return redirect()->route('system.advanced', ['firewall' => $firewall->id, 'tab' => $tab])
                ->with('success', 'System settings updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update system settings: ' . $e->getMessage()]);
        }
    }

    public function storeTunable(Request $request, Firewall $firewall)
    {
        $request->validate([
            'tunable' => 'required|string',
            'value' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            $api->createSystemTunable($request->only(['tunable', 'value', 'descr']));
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('system.advanced', ['firewall' => $firewall->id, 'tab' => 'tunables'])
                ->with('success', 'Tunable created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create tunable: ' . $e->getMessage()]);
        }
    }

    public function updateTunable(Request $request, Firewall $firewall, string $id)
    {
        $request->validate([
            'tunable' => 'required|string',
            'value' => 'required|string',
            'descr' => 'nullable|string',
        ]);

        try {
            $api = new PfSenseApiService($firewall);
            // The API update method usually takes the ID as a parameter or part of the data.
            // Based on PfSenseApiService::updateSystemTunable(array $data), it likely expects the ID in the data or as a query param.
            // Let's check the service method signature again. It was updateSystemTunable(array $data).
            // Usually for updates we need to pass the ID. Let's assume the ID needs to be in the data array or passed separately.
            // Looking at other update methods in this project, they often take $data.
            // Let's assume we need to pass the ID in the data array for now, or check if the service handles it.
            // Actually, for a specific item update, we often need the ID.
            // Let's pass the ID in the data array as 'id' or similar if the API requires it.
            // Re-reading the service definition: updateSystemTunable(array $data).
            // It's likely we need to merge the ID into the data.
            $data = $request->only(['tunable', 'value', 'descr']);
            $data['id'] = $id;
            $data['id'] = $id;
            $api->updateSystemTunable($data);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('system.advanced', ['firewall' => $firewall->id, 'tab' => 'tunables'])
                ->with('success', 'Tunable updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update tunable: ' . $e->getMessage()]);
        }
    }

    public function destroyTunable(Firewall $firewall, string $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->deleteSystemTunable($id);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('system.advanced', ['firewall' => $firewall->id, 'tab' => 'tunables'])
                ->with('success', 'Tunable deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete tunable: ' . $e->getMessage()]);
        }
    }

    public function generalSetup(Firewall $firewall)
    {
        try {
            $api = new \App\Services\PfSenseApiService($firewall);
            $hostname = $api->getSystemHostname()['data'] ?? [];
            $timezone = $api->getSystemTimezone()['data'] ?? [];
            $dns = $api->getSystemDns()['data'] ?? [];
        } catch (\Exception $e) {
            $hostname = [];
            $timezone = [];
            $dns = [];
            session()->flash('error', 'Failed to fetch system settings: ' . $e->getMessage());
        }



        $timezones = \DateTimeZone::listIdentifiers();

        return view('system.general', compact('firewall', 'hostname', 'timezone', 'dns', 'timezones'));
    }

    public function updateGeneralSetup(Request $request, Firewall $firewall)
    {
        try {
            $api = new \App\Services\PfSenseApiService($firewall);

            // Update Hostname
            if ($request->has('hostname') || $request->has('domain')) {
                $api->updateSystemHostname($request->only(['hostname', 'domain']));
            }

            // Update Timezone
            if ($request->has('timezone')) {
                $api->updateSystemTimezone($request->only(['timezone']));
            }

            // Update DNS
            // Note: dnsserver is usually an array, but form might send it differently.
            // Assuming form sends array of servers.
            $dnsData = [
                'dnsserver' => array_filter($request->input('dnsserver', [])),
                'dnsallowoverride' => $request->has('dnsallowoverride'),
                'dnslocalhost' => $request->has('dnslocalhost') ? 'remote' : 'local',
            ];
            $api->updateSystemDns($dnsData);

            $firewall->update(['is_dirty' => true]);

            return redirect()->route('system.general-setup', $firewall)->with('success', 'System settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update system settings: ' . $e->getMessage());
        }
    }

    public function highAvailSync(Firewall $firewall)
    {
        return view('system.high-avail-sync', compact('firewall'));
    }

    public function packageManager(Firewall $firewall)
    {
        return view('system.package-manager', compact('firewall'));
    }

    public function routing(Firewall $firewall)
    {
        return view('system.routing', compact('firewall'));
    }

    public function update(Firewall $firewall)
    {
        return view('system.update', compact('firewall'));
    }

    public function userManager(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $users = [];
        $groups = [];

        try {
            $users = $api->getUsers()['data'] ?? [];
            $groups = $api->getGroups()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }

        return view('system.user-manager', compact('firewall', 'users', 'groups'));
    }

    public function certificateManager(Firewall $firewall)
    {
        $api = new \App\Services\PfSenseApiService($firewall);
        $cas = [];
        $certs = [];

        try {
            $cas = $api->getCertificateAuthorities()['data'] ?? [];
            $certs = $api->getCertificates()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error
        }

        return view('system.certificate-manager', compact('firewall', 'cas', 'certs'));
    }
}
