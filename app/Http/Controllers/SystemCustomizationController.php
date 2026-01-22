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
        
        // Save Status Check Interval (Default 30 if not present, though we handle null in view)
        if ($request->filled('status_check_interval')) {
            SystemSetting::updateOrCreate(['key' => 'status_check_interval'], ['value' => $request->status_check_interval]);
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
