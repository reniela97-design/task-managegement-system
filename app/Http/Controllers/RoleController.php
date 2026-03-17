<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Role::where('role_inactive', false);

        if ($request->filled('search')) {
            $query->where('role_name', 'like', "%{$request->search}%");
        }

        $roles = $query->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        // Check for duplicate role name (R1.3 - Role already in list)
        $existingRole = Role::where('role_name', $request->role_name)
            ->where('role_inactive', false)
            ->first();
        
        if ($existingRole) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Role name is already in the list');
        }

        Role::create([
            'role_name' => $request->role_name,
            'role_user_id' => Auth::id(),
            'role_inactive' => false,
        ]);

        // R1.4 - Redirect to Role List with success message
        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        // Check for duplicate role name (R2.3 - excluding current role)
        $existingRole = Role::where('role_name', $request->role_name)
            ->where('role_inactive', false)
            ->where('role_id', '!=', $role->role_id)
            ->first();
        
        if ($existingRole) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Role name is already in the list');
        }

        $role->update([
            'role_name' => $request->role_name
        ]);

        // R2.3 - Success message "UPDATE SUCCESSFULLY"
        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deleting the Administrator role to avoid locking yourself out (R3.2)
        if ($role->role_name === 'Administrator') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete the Administrator role.');
        }

        $role->update(['role_inactive' => true]);
        
        // R3.2 - Success message after deletion
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}