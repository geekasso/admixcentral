<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemCustomizationController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        // Add current env values if not in settings DB (for display)
        if (!isset($settings['site_url'])) {
            $url = config('app.url');
            $parsed = parse_url($url);
            $settings['site_url'] = $parsed['host'] ?? $url;
            $settings['site_protocol'] = $parsed['scheme'] ?? 'https';
        }

        return view('system.customization.index', compact('settings'));
    }

    /**
     * Simple endpoint to verify hostname reachability.
     * Accessible via CORS to allow pre-switch checks.
     */
    public function checkHostname()
    {
        return response()->json(['status' => 'ok'])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
    }

    /**
     * Server-side proxy to check hostname reachability
     * Avoids Mixed Content (HTTPS -> HTTP) issues in browser
     */
    public function proxyCheck(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 5, 'verify' => false]);
            $response = $client->get($url . '/system/check-hostname');

            if ($response->getStatusCode() === 200) {
                return response()->json(['status' => 'ok']);
            }

            return response()->json(['status' => 'error', 'message' => 'Status: ' . $response->getStatusCode()], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, \App\Services\SystemConfigurationService $configService, \App\Services\SslManagerService $sslManager)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|mimes:ico,png|max:1024',
            'theme' => 'required|in:light,dark',
            'status_check_interval' => 'nullable|integer|min:5|max:300',
            'realtime_interval' => 'nullable|integer|min:2|max:300',
            'fallback_interval' => 'nullable|integer|min:5|max:600',
            'sidebar_bg' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'sidebar_text' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'enable_status_cache' => 'nullable|boolean',
            'site_url' => 'nullable|string|min:3',
            'site_protocol' => 'nullable|in:http,https',
        ]);

        if ($request->filled('site_url') && $request->filled('site_protocol')) {
            $hostname = $request->site_url;
            $scheme = $request->site_protocol;

            // Clean hostname if user entered full URL
            $hostname = str_replace(['http://', 'https://', '/'], '', $hostname);

            // Get Current State
            $currentUrl = config('app.url');
            $parsed = parse_url($currentUrl);
            $currentHost = $parsed['host'] ?? null;
            $currentScheme = $parsed['scheme'] ?? 'http';

            // Detect Hostname Change
            if ($currentHost && $hostname !== $currentHost) {
                // If we were using SSL, we must clean up old certs and force HTTP for new domain
                if ($currentScheme === 'https') {
                    $sslManager->deleteCertificate($currentHost);
                    $scheme = 'http'; // Force HTTP for new domain
                }

                // Apply new Nginx Config (HTTP) via uninstall method (reverts to HTTP standard)
                $sslManager->uninstall($hostname);
            } else {
                // Hostname unchanged, standard update
                $configService->updateSystemHostname($hostname, $scheme);
            }

            SystemSetting::updateOrCreate(['key' => 'site_url'], ['value' => $hostname]);
            SystemSetting::updateOrCreate(['key' => 'site_protocol'], ['value' => $scheme]);

            // Force redirect if the full URL (scheme + host) has changed
            $newBaseUrl = "{$scheme}://{$hostname}";
            if (rtrim($newBaseUrl, '/') !== rtrim(config('app.url'), '/')) {
                return redirect($newBaseUrl . '/system/settings')->with('success', 'System settings updated. Redirecting to new address...');
            }
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('customization', 'public');
            SystemSetting::updateOrCreate(['key' => 'logo_path'], ['value' => Storage::url($path)]);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('customization', 'public');
            SystemSetting::updateOrCreate(['key' => 'favicon_path'], ['value' => Storage::url($path)]);
        }

        SystemSetting::updateOrCreate(['key' => 'theme'], ['value' => $request->theme]);

        // Polling Intervals
        if ($request->filled('status_check_interval')) {
            SystemSetting::updateOrCreate(['key' => 'status_check_interval'], ['value' => $request->status_check_interval]);
        }

        if ($request->filled('realtime_interval')) {
            SystemSetting::updateOrCreate(['key' => 'realtime_interval'], ['value' => $request->realtime_interval]);
        }

        if ($request->filled('fallback_interval')) {
            SystemSetting::updateOrCreate(['key' => 'fallback_interval'], ['value' => $request->fallback_interval]);
        }

        // Sidebar Appearance
        if ($request->filled('sidebar_bg')) {
            SystemSetting::updateOrCreate(['key' => 'sidebar_bg'], ['value' => $request->sidebar_bg]);
        }

        if ($request->filled('sidebar_text')) {
            SystemSetting::updateOrCreate(['key' => 'sidebar_text'], ['value' => $request->sidebar_text]);
        }

        if ($request->has('enable_status_cache')) {
            SystemSetting::updateOrCreate(['key' => 'enable_status_cache'], ['value' => $request->enable_status_cache]);
        }

        return redirect()->route('system.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:logo,favicon',
        ]);

        $key = $request->type === 'logo' ? 'logo_path' : 'favicon_path';

        // Delete the setting to restore default
        SystemSetting::where('key', $key)->delete();

        return redirect()->route('system.settings.index')->with('success', ucfirst($request->type) . ' restored to default.');
    }
}
