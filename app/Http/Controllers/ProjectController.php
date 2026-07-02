<?php

namespace App\Http\Controllers;

use App\Models\Customer;
// use App\Models\Company;
use App\Models\AccountManager;
use App\Models\WorkType;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $projects = Project::with([
            'customer',
            // 'company',
            'workType',
            'picEngineer'
        ])->latest()->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();

        $accountManagers = AccountManager::all();
    
        $workTypes = WorkType::all();
    
        $engineers = User::where('role', 'teknisi')->get();
    
        return view(
            'projects.create',
            compact(
                'customers',
                'accountManagers',
                'workTypes',
                'engineers'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required',
            'account_manager_id' => 'nullable',
            'work_type_id' => 'required',
            'pic_engineer_id' => 'required',

            'project_name' => 'required',

            'project_code' => 'nullable',

            'quotation_number' => 'nullable',

            'status' => 'required',

            'start_date' => 'nullable',

            'end_date' => 'nullable',

            'description' => 'nullable',
        ]);

        $project = Project::create($validated);

        ProjectActivity::create([
            'project_id'    => $project->id,
            'user_id'       => Auth::id(),
            'activity_date' => now(),
            'title'         => 'Project Dibuat',
            'description'   => 'Project baru berhasil dibuat',
        ]);
        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load([
            'customer',
            // 'company',
            'accountManager',
            'workType',
            'picEngineer',
            'supports.engineer',
            'documents',
            'tasks.engineer',
            'activities.user',
        ]);
    
        return view(
            'projects.show',
            compact('project')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
