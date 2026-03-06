<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

abstract class Controller
{
    /**
     * Helper method to record system activity logs.
     * Call this from any controller using: $this->logActivity('Action Description');
     *
     * @param string $description
     * @return void
     */
    protected function logActivity(string $description): void
    {
        // Ensure user is logged in (Activity table requires a user_id)
        if (Auth::check()) {
            Activity::create([
                'activity_description' => $description,
                'activity_user_id' => Auth::id(),
                'activity_ip_address' => request()->ip(),
                'activity_agent' => request()->userAgent(),
                // 'activity_log_datetime' is handled automatically by the DB (useCurrent)
            ]);
        }
    }
}