<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class UserManagerController extends Controller
{
    public function index(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $tab = $request->query('tab', 'users');

        $data = [];
        try {
            switch ($tab) {
                case 'users':
                    $data['users'] = $api->getSystemUsers()['data'] ?? [];
                    break;
                case 'groups':
                    $data['groups'] = $api->getSystemGroups()['data'] ?? [];
                    break;
                case 'auth_servers':
                    $data['auth_servers'] = $api->getSystemAuthServers()['data'] ?? [];
                    break;
            }
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }

        return view('system.user_manager.index', compact('firewall', 'tab', 'data'));
    }

    // Users CRUD
    public function createUser(Firewall $firewall)
    {
        $api = new PfSenseApiService($firewall);
        $groups = $api->getSystemGroups()['data'] ?? [];
        return view('system.user_manager.users.create', compact('firewall', 'groups'));
    }

    public function storeUser(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $data = $request->except(['_token']);
        $data['disabled'] = $request->has('disabled');

        // Ensure password confirmation matches if handled by frontend, or just send password
        // API likely expects 'password' and maybe 'password_confirm' or just 'password'

        try {
            $api->createSystemUser($data);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'users'])
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function editUser(Firewall $firewall, $id)
    {
        $api = new PfSenseApiService($firewall);
        $users = $api->getSystemUsers()['data'] ?? [];
        $user = collect($users)->firstWhere('id', $id);
        $groups = $api->getSystemGroups()['data'] ?? [];

        if (!$user) {
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'users'])
                ->withErrors(['error' => 'User not found.']);
        }

        return view('system.user_manager.users.edit', compact('firewall', 'user', 'groups'));
    }

    public function updateUser(Firewall $firewall, Request $request, $id)
    {
        $api = new PfSenseApiService($firewall);
        $data = $request->except(['_token', '_method']);
        $data['id'] = $id;
        $data['disabled'] = $request->has('disabled');

        try {
            $api->updateSystemUser($data);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'users'])
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroyUser(Firewall $firewall, $id)
    {
        $api = new PfSenseApiService($firewall);
        try {
            $api->deleteSystemUser($id);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'users'])
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    // Groups CRUD
    public function createGroup(Firewall $firewall)
    {
        return view('system.user_manager.groups.create', compact('firewall'));
    }

    public function storeGroup(Firewall $firewall, Request $request)
    {
        $api = new PfSenseApiService($firewall);
        $data = $request->except(['_token']);

        try {
            $api->createSystemGroup($data);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'groups'])
                ->with('success', 'Group created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create group: ' . $e->getMessage()]);
        }
    }

    public function editGroup(Firewall $firewall, $id)
    {
        $api = new PfSenseApiService($firewall);
        $groups = $api->getSystemGroups()['data'] ?? [];
        $group = collect($groups)->firstWhere('id', $id);

        if (!$group) {
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'groups'])
                ->withErrors(['error' => 'Group not found.']);
        }

        return view('system.user_manager.groups.edit', compact('firewall', 'group'));
    }

    public function updateGroup(Firewall $firewall, Request $request, $id)
    {
        $api = new PfSenseApiService($firewall);
        $data = $request->except(['_token', '_method']);
        $data['id'] = $id;

        try {
            $api->updateSystemGroup($data);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'groups'])
                ->with('success', 'Group updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update group: ' . $e->getMessage()]);
        }
    }

    public function destroyGroup(Firewall $firewall, $id)
    {
        $api = new PfSenseApiService($firewall);
        try {
            $api->deleteSystemGroup($id);
            return redirect()->route('system.user_manager.index', ['firewall' => $firewall->id, 'tab' => 'groups'])
                ->with('success', 'Group deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete group: ' . $e->getMessage()]);
        }
    }
}
