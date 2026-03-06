<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Import Role to find the ID dynamically
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,user_email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Find the 'User' role ID dynamically
        // This is safer than hardcoding '3' in case IDs change
        $userRole = Role::where('role_name', 'User')->first();
        
        // Fallback to 3 if the role isn't found (just in case), but ideally it finds the ID
        $roleId = $userRole ? $userRole->role_id : 3;

        $user = User::create([
            'user_name' => $request->name,
            'user_email' => $request->email,
            'user_password' => Hash::make($request->password),
            'user_role_id' => $roleId, // Assigns the 'User' role automatically
            'user_inactive' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 2. Log the activity
        // We log this AFTER Auth::login so that 'activity_user_id' can grab the new user's ID
        $this->logActivity('New user self-registered: ' . $user->user_name);

        return redirect(route('dashboard', absolute: false));
    }
}