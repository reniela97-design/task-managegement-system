<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of active clients.
     */
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

        // Order by client_log_datetime (your custom created_at column)
        $query->orderBy('client_log_datetime', 'desc');

        $clients = $query->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check for duplicate client name first
        $existingClient = Client::where('client_name', $request->client_name)
            ->where('client_inactive', false)
            ->first();
        
        if ($existingClient) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_error', 'The Client is already in the list');
        }

        // Validate the request - ALL FIELDS REQUIRED
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact_person' => 'required|string|max:255',
            'client_contact_number' => 'required|string|max:20',
        ], [
            'client_name.required' => 'The client/company name field is required.',
            'client_contact_person.required' => 'The contact person field is required.',
            'client_contact_number.required' => 'The contact number field is required.',
        ]);

        // Create new client
        Client::create([
            'client_name' => trim($validated['client_name']),
            'client_contact_person' => trim($validated['client_contact_person']),
            'client_contact_number' => trim($validated['client_contact_number']),
            'client_user_id' => Auth::id(),
            'client_inactive' => false,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully!');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View|RedirectResponse
    {
        // Check if client is inactive
        if ($client->client_inactive) {
            return redirect()->route('clients.index')
                ->with('error', 'Cannot edit an inactive client.');
        }

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        // Check if client is inactive
        if ($client->client_inactive) {
            return redirect()->route('clients.index')
                ->with('error', 'Cannot update an inactive client.');
        }

        // Check for duplicate client name (excluding current client)
        $existingClient = Client::where('client_name', $request->client_name)
            ->where('client_inactive', false)
            ->where('client_id', '!=', $client->client_id)
            ->first();
        
        if ($existingClient) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_error', 'The Client is already in the list');
        }

        // Validate the request - ALL FIELDS REQUIRED
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact_person' => 'required|string|max:255',
            'client_contact_number' => 'required|string|max:20',
        ], [
            'client_name.required' => 'The client/company name field is required.',
            'client_contact_person.required' => 'The contact person field is required.',
            'client_contact_number.required' => 'The contact number field is required.',
        ]);

        // Update client (will work even if data is the same)
        $client->update([
            'client_name' => trim($validated['client_name']),
            'client_contact_person' => trim($validated['client_contact_person']),
            'client_contact_number' => trim($validated['client_contact_number']),
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified client (soft delete by setting inactive).
     */
    public function destroy(Client $client): RedirectResponse
    {
        // Check if already inactive
        if ($client->client_inactive) {
            return redirect()->route('clients.index')
                ->with('error', 'Client is already deleted.');
        }

        // Soft delete by setting inactive
        $client->update(['client_inactive' => true]);
        
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully!');
    }

    /**
     * Display a listing of inactive/deleted clients.
     */
    public function trashed(Request $request): View
    {
        $query = Client::where('client_inactive', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_contact_person', 'like', "%{$search}%");
            });
        }

        $query->orderBy('client_log_datetime', 'desc');
        $trashedClients = $query->get();

        return view('clients.trashed', compact('trashedClients'));
    }

    /**
     * Restore a soft-deleted client.
     */
    public function restore($id): RedirectResponse
    {
        $client = Client::where('client_id', $id)
            ->where('client_inactive', true)
            ->firstOrFail();

        $client->update(['client_inactive' => false]);

        return redirect()->route('clients.index')
            ->with('success', 'Client restored successfully!');
    }

    /**
     * Permanently delete a client from database.
     */
    public function forceDelete($id): RedirectResponse
    {
        $client = Client::where('client_id', $id)
            ->where('client_inactive', true)
            ->firstOrFail();

        $client->forceDelete();

        return redirect()->route('clients.trashed')
            ->with('success', 'Client permanently deleted.');
    }
}