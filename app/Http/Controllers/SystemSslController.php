<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SslManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\SystemSetting;

class SystemSslController extends Controller
{
    public function store(Request $request, SslManagerService $sslManager)
    {
        $request->validate([
            'domain' => 'required|string|regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            'email' => 'required|email',
        ]);

        try {
            $domain = $request->input('domain');
            $result = $sslManager->install($domain, $request->input('email'));

            if ($result['success']) {
                // Update database settings to reflect the change in UI
                SystemSetting::updateOrCreate(['key' => 'site_url'], ['value' => $domain]);
                SystemSetting::updateOrCreate(['key' => 'site_protocol'], ['value' => 'https']);

                return response()->json([
                    'success' => true,
                    'message' => 'SSL Certificate installed successfully. Reloading...',
                    'redirect' => 'https://' . $domain . '/system/settings',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Exception $e) {
            Log::error('SSL Install Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, SslManagerService $sslManager)
    {
        try {
            // Retrieve domain from settings if not provided in request (safer)
            $domain = SystemSetting::where('key', 'site_url')->value('value');

            if (!$domain) {
                return response()->json(['success' => false, 'message' => 'Domain not found in settings.'], 404);
            }

            $result = $sslManager->uninstall($domain);

            if ($result['success']) {
                // Update database settings to revert to HTTP
                SystemSetting::updateOrCreate(['key' => 'site_protocol'], ['value' => 'http']);

                return redirect('http://' . $domain . '/system/settings');
            }

            return redirect()->back()->withErrors(['message' => $result['message']]);

        } catch (\Exception $e) {
            Log::error('SSL Uninstall Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
