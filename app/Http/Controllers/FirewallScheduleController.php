<?php

namespace App\Http\Controllers;

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Http\Request;

class FirewallScheduleController extends Controller
{
    public function index(Firewall $firewall)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $response = $api->getSchedules();
            $schedules = $response['data'] ?? [];

            return view('firewall.schedules.index', compact('firewall', 'schedules'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch schedules: ' . $e->getMessage());
        }
    }

    public function store(Request $request, Firewall $firewall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'descr' => 'nullable|string',
            'timerange' => 'required|array',
            'timerange.*.month' => 'nullable',
            'timerange.*.day' => 'nullable',
            'timerange.*.hour' => 'nullable|string',
            'timerange.*.position' => 'nullable|string',
            'timerange.*.rangedescr' => 'nullable|string',
        ]);

        // Transform payload
        $data = [
            'name' => $validated['name'],
            'descr' => $validated['descr'] ?? '',
            'timerange' => []
        ];

        foreach ($validated['timerange'] as $range) {
            $item = [];
            if (!empty($range['month'])) {
                $item['month'] = [(int) $range['month']];
            }
            if (!empty($range['day'])) {
                $item['day'] = [(int) $range['day']];
            }
            if (!empty($range['hour'])) {
                $item['hour'] = $range['hour'];
            }
            if (!empty($range['rangedescr'])) {
                $item['rangedescr'] = $range['rangedescr'];
            }
            // Only add if at least one field is present
            if (!empty($item)) {
                $data['timerange'][] = $item;
            }
        }

        try {
            $api = new PfSenseApiService($firewall);
            $api->createSchedule($data);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('firewall.schedules.index', $firewall)
                ->with('success', 'Schedule created successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create schedule: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Firewall $firewall, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'descr' => 'nullable|string',
            'timerange' => 'required|array',
            'timerange.*.month' => 'nullable',
            'timerange.*.day' => 'nullable',
            'timerange.*.hour' => 'nullable|string',
            'timerange.*.position' => 'nullable|string',
            'timerange.*.rangedescr' => 'nullable|string',
        ]);

        // Transform payload
        $data = [
            'name' => $validated['name'],
            'descr' => $validated['descr'] ?? '',
            'timerange' => []
        ];

        foreach ($validated['timerange'] as $range) {
            $item = [];
            if (!empty($range['month'])) {
                $item['month'] = [(int) $range['month']];
            }
            if (!empty($range['day'])) {
                $item['day'] = [(int) $range['day']];
            }
            if (!empty($range['hour'])) {
                $item['hour'] = $range['hour'];
            }
            if (!empty($range['rangedescr'])) {
                $item['rangedescr'] = $range['rangedescr'];
            }
            if (!empty($item)) {
                $data['timerange'][] = $item;
            }
        }

        try {
            $api = new PfSenseApiService($firewall);
            $api->updateSchedule((int) $id, $data);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('firewall.schedules.index', $firewall)
                ->with('success', 'Schedule updated successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    public function destroy(Firewall $firewall, $id)
    {
        try {
            $api = new PfSenseApiService($firewall);
            $api->deleteSchedule((int) $id);
            $firewall->update(['is_dirty' => true]);

            return redirect()->route('firewall.schedules.index', $firewall)
                ->with('success', 'Schedule deleted successfully. Please apply changes.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}
