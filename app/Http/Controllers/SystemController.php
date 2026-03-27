<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = System::where('system_inactive', false);

        if ($request->filled('search')) {
            $query->where('system_name', 'like', "%{$request->search}%");
        }

        $systems = $query->get();
        return view('systems.index', compact('systems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('systems.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
        ]);

        // Check for duplicate system name (S1.3 - System already in list)
        $existingSystem = System::where('system_name', $request->system_name)
            ->where('system_inactive', false)
            ->first();
        
        if ($existingSystem) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The System name is already in the list');
        }

        System::create([
            'system_name' => $request->system_name,
            'system_user_id' => Auth::id(),
            'system_inactive' => false,
        ]);

        // S1.4 - Redirect to System List with success message
        return redirect()->route('systems.index')
            ->with('success', 'System created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(System $system): View
    {
        return view('systems.edit', compact('system'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, System $system): RedirectResponse
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
        ]);

        // Check for duplicate system name (S2.3 - excluding current system)
        $existingSystem = System::where('system_name', $request->system_name)
            ->where('system_inactive', false)
            ->where('system_id', '!=', $system->system_id)
            ->first();
        
        if ($existingSystem) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The System name is already in the list');
        }

        $system->update([
            'system_name' => $request->system_name
        ]);

        // S2.3 - Success message "UPDATE SUCCESSFULLY"
        return redirect()->route('systems.index')
            ->with('success', 'System updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(System $system): RedirectResponse
    {
        $system->update(['system_inactive' => true]);
        
        // S3.2 - Success message after deletion
        return redirect()->route('systems.index')
            ->with('success', 'System deleted successfully!');
    }
}