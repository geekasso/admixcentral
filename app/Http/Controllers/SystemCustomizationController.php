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
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/customization');
            SystemSetting::updateOrCreate(['key' => 'logo_path'], ['value' => Storage::url($path)]);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('public/customization');
            SystemSetting::updateOrCreate(['key' => 'favicon_path'], ['value' => Storage::url($path)]);
        }

        SystemSetting::updateOrCreate(['key' => 'theme'], ['value' => $request->theme]);

        return redirect()->route('system.customization.index')->with('success', 'Customization settings updated successfully.');
    }
}
