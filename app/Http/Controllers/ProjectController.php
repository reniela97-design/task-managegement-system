<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Project::where('project_inactive', false)->with('client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($user->hasRole('Administrator')) {
            $projects = $query->get();
        } elseif ($user->hasRole('Manager')) {
            $managerRoleIds = Role::where('role_name', 'Manager')->pluck('role_id');
            $managerUserIds = User::whereIn('user_role_id', $managerRoleIds)->pluck('user_id');

            $projects = $query->where(function ($q) use ($user, $managerUserIds) {
                $q->where('project_user_id', $user->user_id)
                  ->orWhereIn('project_user_id', $managerUserIds);
            })->get();
        } else {
            $projects = $query->where('project_user_id', $user->user_id)->get();
        }

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        if (!Auth::user()->hasRole('Administrator') && !Auth::user()->hasRole('Manager')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::where('client_inactive', false)->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->hasRole('Administrator') && !Auth::user()->hasRole('Manager')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_client_id' => 'required|exists:clients,client_id',
            'project_address' => 'nullable|string',
        ]);

        Project::create([
            'project_name' => $validated['project_name'],
            'project_client_id' => $validated['project_client_id'],
            'project_address' => $validated['project_address'],
            'project_user_id' => Auth::id(),
            'project_branch' => 'Main',
            'project_inactive' => false,
        ]);

        $this->logActivity('Created new project: ' . $validated['project_name']);

        return redirect()->route('projects.index')->with('status', 'Project created successfully!');
    }

    public function edit(Project $project): View
    {
        $this->authorizeProjectAccess($project);
        
        $clients = Client::where('client_inactive', false)->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectAccess($project);
        
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_client_id' => 'required|exists:clients,client_id',
            'project_address' => 'nullable|string',
        ]);

        $project->update([
            'project_name' => $validated['project_name'],
            'project_client_id' => $validated['project_client_id'],
            'project_address' => $validated['project_address'],
        ]);

        $this->logActivity('Updated project: ' . $project->project_name);

        return redirect()->route('projects.index')->with('status', 'Project updated successfully!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if (!Auth::user()->hasRole('Administrator') && !Auth::user()->hasRole('Manager')) {
             abort(403, 'Unauthorized action.');
        }

        $project->update(['project_inactive' => true]);

        $this->logActivity('Deleted project: ' . $project->project_name);

        return redirect()->route('projects.index')->with('status', 'Project deleted successfully!');
    }

    private function authorizeProjectAccess($project)
    {
        $user = Auth::user();
        if ($user->hasRole('Administrator')) return true;
        
        if ($project->project_inactive) {
            abort(404);
        }

        if ($project->project_user_id !== $user->user_id) {
            abort(403, 'Unauthorized access to this project.');
        }
    }
}