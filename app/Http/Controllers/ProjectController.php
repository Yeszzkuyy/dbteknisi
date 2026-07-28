<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use App\Models\AccountManager;
use App\Models\WorkType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Project\ProjectService;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        // Ambil project dengan status Open atau Progress
        $projects = Project::with(['customer', 'workType']) // ← HAPUS picEngineer
            ->whereIn('status', ['Open', 'Progress'])
            ->latest()
            ->get();

        // Total semua project
        $totalProjects = Project::count();

        // Total project aktif
        $activeProjects = Project::whereIn('status', ['Open', 'Progress'])->count();

        return view('projects.index', compact('projects', 'totalProjects', 'activeProjects'));
    }

    public function create(Request $request)
    {
        $customerId = $request->query('customer_id');
        
        if (!$customerId) {
            return redirect()->route('customers.index')
                ->with('error', 'Silakan pilih customer terlebih dahulu.');
        }
        
        $customer = Customer::findOrFail($customerId);
        $workTypes = WorkType::all();
        $accountManagers = AccountManager::all();
        
        return view('projects.create', compact('customer', 'workTypes', 'accountManagers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'work_type_id' => 'required|exists:work_types,id',
            'account_manager_id' => 'nullable|exists:account_managers,id',
            'pic_engineer_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $project = $this->projectService->create($validated);

        return redirect()
            ->route('customers.show', $project->customer_id)
            ->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        $project->load([
            'customer', 
            'workType', 
            'accountManager', 
            'tasks', 
            'documents', 
            'supports', 
            'activities'
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $customers = Customer::all();
        $workTypes = WorkType::all();
        $accountManagers = AccountManager::all();
        $engineers = User::where('role', 'teknisi')->get();
        
        return view('projects.edit', compact('project', 'customers', 'workTypes', 'accountManagers', 'engineers'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'work_type_id' => 'required|exists:work_types,id',
            'account_manager_id' => 'nullable|exists:account_managers,id',
            'pic_engineer' => 'required|string|max:255',
            'support_technicians' => 'nullable|string|max:500',
            'status' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);
    
        $project->update($validated);
    
        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}