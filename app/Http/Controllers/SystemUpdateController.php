<?php

namespace App\Http\Controllers;

use App\Models\SystemUpdate;
use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemUpdateController extends Controller
{
    protected $updateService;

    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    /**
     * Display the update dashboard.
     */
    public function index()
    {
        // Get current version from VERSION file or config
        $currentVersion = 'v0.0.0';
        if (File::exists(base_path('VERSION'))) {
            $currentVersion = trim(File::get(base_path('VERSION')));
        }

        // Get the latest update record
        $latestUpdate = SystemUpdate::latest()->first();
        $isNewVersionAvailable = false;

        if ($latestUpdate && $latestUpdate->status !== 'complete' && $latestUpdate->status !== 'failed') {
            // If we have a pending/active update, show it
        } else {
            // Check if the latest *completed* update is newer? 
            // Or just rely on the "checkForUpdates" action to populate a new record.
        }

        return view('system.updates.index', compact('currentVersion', 'latestUpdate'));
    }

    /**
     * Check for updates via Service.
     */
    public function check(Request $request)
    {
        $release = $this->updateService->checkForUpdates();
        $currentVersion = trim(File::get(base_path('VERSION')));

        if (!$release) {
            if ($request->wantsJson()) {
                return response()->json([
                    'update_available' => false,
                    'message' => 'No updates found or failed to fetch.',
                    'current_version' => $currentVersion
                ]);
            }
            return back()->with('status', 'No updates found or failed to fetch.');
        }

        if ($this->updateService->isNewVersionAvailable($currentVersion, $release['tag_name'])) {

            // Create a pending record if one doesn't exist for this version
            $exists = SystemUpdate::where('available_version', $release['tag_name'])->exists();
            if (!$exists) {
                SystemUpdate::create([
                    'current_version' => $currentVersion,
                    'available_version' => $release['tag_name'],
                    'status' => 'idle',
                    'requested_by' => auth()->id(),
                    'log' => ['Update found: ' . $release['tag_name']],
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'update_available' => true,
                    'version' => $release['tag_name'],
                    'message' => 'New version available: ' . $release['tag_name']
                ]);
            }

            return back()->with('status', 'New version available: ' . $release['tag_name']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'update_available' => false,
                'message' => 'You are on the latest version.',
                'current_version' => $currentVersion
            ]);
        }

        return back()->with('status', 'You are on the latest version.');
    }

    /**
     * Start the download/install process.
     */
    public function store(Request $request)
    {
        $version = $request->input('version');
        $update = SystemUpdate::where('available_version', $version)->firstOrFail();

        if ($update->status === 'idle' || $update->status === 'failed') {
            $update->status = 'pending_install';
            $update->requested_at = now();
            $update->save();

            // Trigger the daemon command in background (or simpler: let the daemon poll)
            // For immediate feedback in dev, you might dispatch a job. 
            // Here we just set status so the daemon (cron/supervisor) picks it up.

            return back()->with('status', 'Update queued. The system will start the update shortly.');
        }

        return back()->with('error', 'Update already in progress.');
    }
}
