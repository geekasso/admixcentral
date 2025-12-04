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

        if ($user->role === 'admin') {
            $firewalls = Firewall::with('company')->get();
        } else {
            $firewalls = Firewall::where('company_id', $user->company_id)->get();
        }

        return view('firewalls.index', compact('firewalls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('firewalls.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Firewall::create($validated);

        return redirect()->route('firewalls.index')->with('success', 'Firewall created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Firewall $firewall)
    {
        return redirect()->route('firewall.dashboard', $firewall);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Firewall $firewall)
    {
        $companies = Company::all();
        return view('firewalls.edit', compact('firewall', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Firewall $firewall)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $firewall->update($validated);

        return redirect()->route('firewalls.index')->with('success', 'Firewall updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Firewall $firewall)
    {
        $firewall->delete();

        return redirect()->route('firewalls.index')->with('success', 'Firewall deleted successfully.');
    }
}
