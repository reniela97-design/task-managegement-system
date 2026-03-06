<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StatusController extends Controller
{
    public function index(): View
    {
        $statuses = Status::where('status_inactive', false)->get();
        return view('statuses.index', compact('statuses'));
    }

    public function create(): View
    {
        return view('statuses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status_name' => 'required|string|max:255',
            'status_color' => 'nullable|string|max:7', // Hex code ex: #ff0000
        ]);

        Status::create([
            'status_name' => $validated['status_name'],
            'status_color' => $validated['status_color'] ?? '#cccccc',
            'status_inactive' => false,
        ]);

        return redirect()->route('statuses.index')->with('status', 'Status created successfully!');
    }

    public function edit(Status $status): View
    {
        return view('statuses.edit', compact('status'));
    }

    public function update(Request $request, Status $status): RedirectResponse
    {
        $validated = $request->validate([
            'status_name' => 'required|string|max:255',
            'status_color' => 'nullable|string|max:7',
        ]);

        $status->update($validated);

        return redirect()->route('statuses.index')->with('status', 'Status updated successfully!');
    }

    public function destroy(Status $status): RedirectResponse
    {
        $status->update(['status_inactive' => true]);
        return redirect()->route('statuses.index')->with('status', 'Status deleted successfully!');
    }
}