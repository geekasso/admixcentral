<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();

        if ($currentUser->isGlobalAdmin()) {
            $users = User::with('company')->get();
        } elseif ($currentUser->isCompanyAdmin()) {
            $users = User::where('company_id', $currentUser->company_id)->with('company')->get();
        } else {
            abort(403);
        }

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,user'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        // Role enforcement logic
        if ($currentUser->isCompanyAdmin()) {
            // Company Admin can only create users for their own company
            $validated['company_id'] = $currentUser->company_id;
            // Company Admin cannot create Global Admins (no company_id)
            if (empty($validated['company_id'])) {
                abort(403, 'Company Admins must assign users to their company.');
            }
        } elseif ($currentUser->isGlobalAdmin()) {
            // Global Admin logic
            if ($validated['role'] === 'admin' && empty($validated['company_id'])) {
                // Creating a Global Admin
            } elseif (!empty($validated['company_id'])) {
                // Creating a Company Admin or User
            } else {
                // Creating a user without company? Allow for now, maybe unassigned
            }
        } else {
            abort(403);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company_id' => $validated['company_id'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Access control
        if ($currentUser->isCompanyAdmin()) {
            if ($user->company_id !== $currentUser->company_id) {
                abort(403);
            }
        } elseif (!$currentUser->isGlobalAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,user'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Role enforcement logic for update
        if ($currentUser->isCompanyAdmin()) {
            $validated['company_id'] = $currentUser->company_id;
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'company_id' => $validated['company_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        if ($currentUser->isCompanyAdmin()) {
            if ($user->company_id !== $currentUser->company_id) {
                abort(403);
            }
        } elseif (!$currentUser->isGlobalAdmin()) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
