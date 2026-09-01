<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function create(Project $project)
    {
        $this->authorize('update', $project);

        $engineers = User::where('role', 'teknisi')->get();

        return view(
            'project_tasks.create',
            compact(
                'project',
                'engineers'
            )
        );
    }

    public function store(
        Request $request,
        Project $project
    )
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'assigned_to' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'required',
            'start_date' => 'nullable',
            'due_date' => 'nullable',
        ]);

        $project->tasks()->create(
            $validated
        );

        return redirect()
            ->route(
                'projects.show',
                $project
            );
    }

    public function destroy(
        ProjectTask $projectTask
    )
    {
        $this->authorize('update', $projectTask->project);

        $project = $projectTask->project;

        $projectTask->delete();

        return redirect()
            ->route(
                'projects.show',
                $project
            );
    }
}