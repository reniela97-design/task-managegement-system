<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function index(Request $request): View
    {
        $query = System::where('system_inactive', false);

        if ($request->filled('search')) {
            $query->where('system_name', 'like', "%{$request->search}%");
        }

        $systems = $query->get();
        return view('systems.index', compact('systems'));
    }

    public function create(): View
    {
        return view('systems.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
        ]);

        System::create([
            'system_name' => $validated['system_name'],
            'system_user_id' => Auth::id(),
            'system_inactive' => false,
        ]);

        $this->logActivity('Created new system: ' . $validated['system_name']);

        return redirect()->route('systems.index')->with('status', 'System created successfully!');
    }

    public function edit(System $system): View
    {
        return view('systems.edit', compact('system'));
    }

    public function update(Request $request, System $system): RedirectResponse
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
        ]);

        $system->update($validated);
        $this->logActivity('Updated system: ' . $system->system_name);

        return redirect()->route('systems.index')->with('status', 'System updated successfully!');
    }

    public function destroy(System $system): RedirectResponse
    {
        $system->update(['system_inactive' => true]);
        $this->logActivity('Deleted system: ' . $system->system_name);

        return redirect()->route('systems.index')->with('status', 'System deleted successfully!');
    }
}