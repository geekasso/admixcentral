<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class SystemRestApiController extends Controller
{
    public function index(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $installedVersion = 'Unknown';
        $latestVersion = 'Unknown';
        $releaseDate = 'Unknown';
        $updateAvailable = false;

        // Fetch Installed Version
        try {
            $versionResponse = $api->getApiVersion();
            $installedVersion = $versionResponse['data']['output'] ?? 'Unknown';
            // Clean up version string
            $installedVersion = trim($installedVersion);
        } catch (\Exception $e) {
            // Keep default
        }

        // Fetch Latest Version from GitHub
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://api.github.com/repos/jaredhendrickson13/pfsense-api/releases/latest');
            if ($response->successful()) {
                $releaseData = $response->json();
                $latestVersion = str_replace('v', '', $releaseData['tag_name'] ?? 'Unknown');
                $releaseDate = \Carbon\Carbon::parse($releaseData['published_at'])->format('M d, Y');
            }
        } catch (\Exception $e) {
            // Keep default
        }

        // Compare Versions
        if ($installedVersion !== 'Unknown' && $latestVersion !== 'Unknown') {
            $updateAvailable = version_compare($installedVersion, $latestVersion, '<');
        }

        return view('system.rest-api.index', compact('firewall', 'installedVersion', 'latestVersion', 'releaseDate', 'updateAvailable'));
    }

    public function update(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);

        try {
            // Repo update failed to find the package. Use direct fetch from GitHub releases.
            // Target Version: 2.6.7
            $url = 'https://github.com/jaredhendrickson13/pfsense-api/releases/download/v2.6.7/pfSense-pkg-RESTAPI-2.6.7.pkg';
            $command = "fetch -o /tmp/pfSense-pkg-RESTAPI.pkg {$url} && pkg install -y -f /tmp/pfSense-pkg-RESTAPI.pkg";

            // Re-using the command prompt capability
            $response = $api->commandPrompt($command); // Assuming commandPrompt maps to diagnosticsCommandPrompt logic we verified

            // We might want to pass the output back, or just success message
            // The command might take a while, but standard timeout is usually generous.
            // If it restarts the PHP process/web server, we might lose connection, so catch that.

            $output = $response['data']['output'] ?? 'Command executed.';

            return back()->with('success', 'REST API update initiated. Output: ' . substr($output, 0, 200) . '...');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update REST API: ' . $e->getMessage());
        }
    }
}
