<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User; // <-- Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View // <-- Add Request parameter
    {
        $currentUser = Auth::user();
        
        // 1. Start with the base query
        $query = Activity::with('user.role')
                         ->orderBy('activity_log_datetime', 'desc');

        // 2. Apply Filters based on Role
        if ($currentUser->hasRole('Administrator')) {
            // ADMIN: Sees EVERYTHING (No filter added)
        } 
        else {
            // STANDARD USER: Sees ONLY their own logs
            $query->where('activity_user_id', $currentUser->user_id);
        }

        // 3. Apply Account Filter (If requested by Admin or Manager)
        if ($currentUser->hasRole('Administrator') && $request->filled('user_id')) {
            $query->where('activity_user_id', $request->user_id);
        }

        // Add withQueryString() so the filter stays active when clicking page 2, 3, etc.
        $activities = $query->paginate(20)->withQueryString();

        // 4. Fetch users for the dropdown (only needed for Admins/Managers)
        $users = collect();
        if ($currentUser->hasRole('Administrator')) {
            $users = User::where('user_inactive', false)->get();
        }

        return view('activity.index', compact('activities', 'users'));
    }
}