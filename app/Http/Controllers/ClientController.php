<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::where('client_inactive', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_contact_person', 'like', "%{$search}%");
            });
        }

        $clients = $query->get();
        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact_person' => 'nullable|string|max:255',
            'client_contact_number' => 'nullable|string|max:20',
        ]);

        Client::create([
            'client_name' => $validated['client_name'],
            'client_contact_person' => $validated['client_contact_person'],
            'client_contact_number' => $validated['client_contact_number'],
            'client_user_id' => Auth::id(),
            'client_inactive' => false,
        ]);

        return redirect()->route('clients.index')->with('status', 'Client created successfully!');
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact_person' => 'nullable|string|max:255',
            'client_contact_number' => 'nullable|string|max:20',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('status', 'Client updated successfully!');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->update(['client_inactive' => true]);
        return redirect()->route('clients.index')->with('status', 'Client deleted successfully!');
    }
}