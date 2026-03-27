<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Type::where('type_inactive', false);

        if ($request->filled('search')) {
            $query->where('type_name', 'like', "%{$request->search}%");
        }

        $types = $query->get();
        return view('types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        // Check for duplicate type name (T1.3 - Type already in list)
        $existingType = Type::where('type_name', $request->type_name)
            ->where('type_inactive', false)
            ->first();
        
        if ($existingType) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Type name is already in the list');
        }

        Type::create([
            'type_name' => $request->type_name,
            'type_user_id' => Auth::id(),
            'type_inactive' => false,
        ]);

        // T1.4 - Redirect to Type List with success message
        return redirect()->route('types.index')
            ->with('success', 'Type created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Type $type): View
    {
        return view('types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Type $type): RedirectResponse
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        // Check for duplicate type name (T2.3 - excluding current type)
        $existingType = Type::where('type_name', $request->type_name)
            ->where('type_inactive', false)
            ->where('type_id', '!=', $type->type_id)
            ->first();
        
        if ($existingType) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The Type name is already in the list');
        }

        $type->update([
            'type_name' => $request->type_name
        ]);

        // T2.3 - Success message "UPDATE SUCCESSFULLY"
        return redirect()->route('types.index')
            ->with('success', 'Type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type): RedirectResponse
    {
        $type->update(['type_inactive' => true]);
        
        // T3.2 - Success message after deletion
        return redirect()->route('types.index')
            ->with('success', 'Type deleted successfully!');
    }
}