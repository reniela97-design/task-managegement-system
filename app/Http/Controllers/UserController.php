<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();
        
        $users = User::with('role')
            ->where('user_inactive', false)
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'like', '%Admin%')
                      ->orWhere('role_name', 'like', '%Manager%');
            })->get();
            
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();
        $roles = Role::where('role_inactive', false)
            ->where(function($query) {
                $query->where('role_name', 'like', '%Admin%')
                      ->orWhere('role_name', 'like', '%Manager%');
            })->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,user_email',
            'user_password' => 'required|string|min:8',
            'user_role_id' => 'required|exists:roles,role_id',
        ]);

        User::create([
            'user_name' => $validated['user_name'],
            'user_email' => $validated['user_email'],
            'user_password' => Hash::make($validated['user_password']),
            'user_role_id' => $validated['user_role_id'],
            'user_inactive' => false,
        ]);

        // LOG ACTIVITY
        $this->logActivity('Created new user account: ' . $validated['user_name']);

        return redirect()->route('users.index')->with('status', 'User account created successfully!');
    }

    public function edit(User $user): View
    {
        $this->authorizeAdmin();
        $roles = Role::where('role_inactive', false)
            ->where(function($query) {
                $query->where('role_name', 'like', '%Admin%')
                      ->orWhere('role_name', 'like', '%Manager%');
            })->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => ['required', 'email', Rule::unique('users', 'user_email')->ignore($user->user_id, 'user_id')],
            'user_role_id' => 'required|exists:roles,role_id',
        ]);

        $user->user_name = $validated['user_name'];
        $user->user_email = $validated['user_email'];
        $user->user_role_id = $validated['user_role_id'];

        if ($request->filled('user_password')) {
            $request->validate(['user_password' => 'string|min:8']);
            $user->user_password = Hash::make($request->user_password);
        }

        $user->save();

        // LOG ACTIVITY
        $this->logActivity('Updated user account: ' . $user->user_name);

        return redirect()->route('users.index')->with('status', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($user->user_id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->update(['user_inactive' => true]);

        // LOG ACTIVITY
        $this->logActivity('Deactivated user account: ' . $user->user_name);

        return redirect()->route('users.index')->with('status', 'User deactivated successfully!');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action. Only Administrators can manage accounts.');
        }
    }
}