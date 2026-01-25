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
        return view('system.customization.index', compact('settings'));
    }

    public function update(Request $request)
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
        ]);

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
