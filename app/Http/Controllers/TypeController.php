<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Type::where('type_inactive', false);

        if ($request->filled('search')) {
            $query->where('type_name', 'like', "%{$request->search}%");
        }

        $types = $query->get();
        return view('types.index', compact('types'));
    }

    public function create(): View
    {
        return view('types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        Type::create([
            'type_name' => $validated['type_name'],
            'type_user_id' => Auth::id(),
            'type_inactive' => false,
        ]);

        $this->logActivity('Created new type: ' . $validated['type_name']);

        return redirect()->route('types.index')->with('status', 'Type created successfully!');
    }

    public function edit(Type $type): View
    {
        return view('types.edit', compact('type'));
    }

    public function update(Request $request, Type $type): RedirectResponse
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $type->update($validated);
        $this->logActivity('Updated type: ' . $type->type_name);

        return redirect()->route('types.index')->with('status', 'Type updated successfully!');
    }

    public function destroy(Type $type): RedirectResponse
    {
        $type->update(['type_inactive' => true]);
        $this->logActivity('Deleted type: ' . $type->type_name);

        return redirect()->route('types.index')->with('status', 'Type deleted successfully!');
    }
}