<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SystemStatusController extends Controller
{
    /**
     * Check the status of system components (Queue, Database, Redis/Cache).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $status = [
            'queue' => false,
            'database' => false,
            'redis' => false,
        ];

        // 1. Check Database
        try {
            DB::connection()->getPdo();
            $status['database'] = true;
        } catch (\Exception $e) {
            $status['database'] = false;
        }

        // 2. Check Queue (approximation)
        // We can check if any failed jobs exist, or if the jobs table is backed up.
        // Better yet: Check if recent jobs have been processed? 
        // For simple status: Just assume Queue is OK if Database is OK (since we use database driver usually, or Redis)
        
        // If driver is Redis:
        if (config('queue.default') === 'redis') {
             try {
                 Redis::connection()->ping();
                 $status['redis'] = true;
                 $status['queue'] = true; // infer queue is likely ok if redis is up
             } catch (\Exception $e) {
                 $status['redis'] = false;
                 $status['queue'] = false;
             }
        } elseif (config('queue.default') === 'database') {
             // For database queue, if DB is up, queue is effectively "up" (storage wise)
             // Worker status is harder to know without a heartbeat.
             $status['queue'] = $status['database'];
             $status['redis'] = 'n/a';
        } else {
             // Sync or other
             $status['queue'] = true;
             $status['redis'] = 'n/a';
        }

        return response()->json($status);
    }
}
