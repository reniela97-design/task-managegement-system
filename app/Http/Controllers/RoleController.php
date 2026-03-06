<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function index(): View
    {
        // Only fetch active roles
        $roles = Role::where('role_inactive', false)->get();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name',
        ]);

        Role::create([
            'role_name' => $validated['role_name'],
            'role_user_id' => Auth::id(), // Log who created it
            'role_inactive' => false,
        ]);

        return redirect()->route('roles.index')->with('status', 'Role created successfully!');
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name,' . $role->role_id . ',role_id',
        ]);

        $role->update([
            'role_name' => $validated['role_name'],
        ]);

        return redirect()->route('roles.index')->with('status', 'Role updated successfully!');
    }

    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deleting the Administrator role to avoid locking yourself out
        if ($role->role_name === 'Administrator') {
            return back()->with('error', 'Cannot delete the Administrator role.');
        }

        $role->update(['role_inactive' => true]);
        return redirect()->route('roles.index')->with('status', 'Role deleted successfully!');
    }
}