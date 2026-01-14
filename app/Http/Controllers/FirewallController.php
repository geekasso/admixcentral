<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Models\Company;
use Illuminate\Http\Request;

class FirewallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isGlobalAdmin()) {
            $firewalls = Firewall::with('company')->get();
        } else {
            $firewalls = Firewall::where('company_id', $user->company_id)->get();
        }

        return view('firewalls.index', compact('firewalls'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->isGlobalAdmin()) {
            $companies = Company::all();
        } else {
            $companies = collect([$user->company]);
        }
        return view('firewalls.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'auth_method' => 'required|in:basic,token',
            'api_key' => 'required_if:auth_method,basic|nullable|string',
            'api_secret' => 'required_if:auth_method,basic|nullable|string',
            'api_token' => 'required_if:auth_method,token|nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($user->isCompanyAdmin()) {
            if ((int) $validated['company_id'] !== (int) $user->company_id) {
                abort(403, 'You can only create firewalls for your own company.');
            }
        } elseif (!$user->isGlobalAdmin()) {
            abort(403);
        }

        $firewall = new Firewall($validated);

        try {
            $api = new \App\Services\PfSenseApiService($firewall);
            // We need to set credentials manually on the service if the model isn't saved/mutated yet?
            // PfSenseApiService constructor uses model attributes.
            // Model attributes are set in new Firewall($validated).
            // Encrypted casting might not happen if not saving? 
            // Actually, casts happen on set/save. If we just 'new', attributes are raw.
            // But Service expects raw/decrypted.
            // Wait, if I set 'api_key' on model, it might auto-encrypt if cast is 'encrypted'.
            // If I read it back, it decrypts.
            // So new Firewall($validated) should work in memory.

            $response = $api->get('/status/system');
            if (isset($response['data']['netgate_id'])) {
                $validated['netgate_id'] = $response['data']['netgate_id'];
            }
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Connection failed: ' . $e->getMessage())
                ->withErrors(['url' => 'Connection failed: ' . $e->getMessage()]);
        }

        $firewall = Firewall::create($validated);

        if (!$firewall->netgate_id) {
            $firewall->netgate_id = (string) $firewall->id;
            $firewall->save();
        }

        return redirect()->route('firewalls.index')->with('success', 'Firewall created successfully.');
    }

    public function show(Firewall $firewall)
    {
        // Middleware EnsureTenantScope handles access, but good to be explicit or leave it.
        // We'll rely on middleware for 'firewall' bound model access, 
        // but here it's implicit binding.
        // EnsureTenantScope usually applies to routes under /firewall/{firewall}.
        // This specific route might not be covered if not in that group?
        // Route::resource('firewalls') has middleware EnsureTenantScope applied in web.php.
        return redirect()->route('firewall.dashboard', $firewall);
    }

    public function edit(Firewall $firewall)
    {
        // Scope check
        $user = auth()->user();
        if ($user->isCompanyAdmin() && $firewall->company_id !== $user->company_id) {
            abort(403);
        }

        if ($user->isGlobalAdmin()) {
            $companies = Company::all();
        } else {
            $companies = collect([$user->company]);
        }
        return view('firewalls.edit', compact('firewall', 'companies'));
    }

    public function update(Request $request, Firewall $firewall)
    {
        $user = auth()->user();
        if ($user->isCompanyAdmin() && $firewall->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'auth_method' => 'required|in:basic,token',
            'api_key' => 'required_if:auth_method,basic|nullable|string',
            'api_secret' => 'nullable|string',
            'api_token' => 'required_if:auth_method,token|nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($user->isCompanyAdmin()) {
            if ((int) $validated['company_id'] !== (int) $user->company_id) {
                // Prevent moving firewall to another company
                abort(403);
            }
        }

        if ($validated['auth_method'] === 'token') {
            $validated['api_key'] = null;
            $validated['api_secret'] = null;
        } else {
            $validated['api_token'] = null;
            if (empty($validated['api_secret'])) {
                if (empty($firewall->api_secret) && $firewall->auth_method !== 'basic') {
                    return back()->withErrors(['api_secret' => 'Password is required when switching to Basic Authentication.'])->withInput();
                }
                unset($validated['api_secret']);
            }
        }

        $firewall->update($validated);

        return redirect()->route('firewalls.index')->with('success', 'Firewall updated successfully.');
    }

    public function destroy(Firewall $firewall)
    {
        $user = auth()->user();
        if ($user->isCompanyAdmin() && $firewall->company_id !== $user->company_id) {
            abort(403);
        }
        if (!$user->isGlobalAdmin() && !$user->isCompanyAdmin()) {
            abort(403);
        }

        $firewall->delete();

        return redirect()->route('firewalls.index')->with('success', 'Firewall deleted successfully.');
    }
}
