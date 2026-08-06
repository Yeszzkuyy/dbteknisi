<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    public function created(Project $project): void
    {
        ProjectActivity::create([
            'project_id'    => $project->id,
            'user_id'       => Auth::id(),
            'activity_date' => now(),
            'title'         => 'Project Dibuat',
            'description'   => 'Project baru berhasil dibuat',
        ]);
    }

    public function updated(Project $project): void
    {
        if (! $project->wasChanged('project_status_id')) {
            return;
        }

        $oldStatus = ProjectStatus::find($project->getOriginal('project_status_id'))?->name;

        ProjectActivity::create([
            'project_id'    => $project->id,
            'user_id'       => Auth::id(),
            'activity_date' => now(),
            'title'         => 'Status Diubah',
            'description'   => "Status project diubah dari {$oldStatus} menjadi {$project->status?->name}",
        ]);
    }

    public function deleted(Project $project): void
    {
        $project->documents()->get()->each(fn ($document) => $document->delete());
        $project->tasks()->get()->each(fn ($task) => $task->delete());
        $project->supports()->get()->each(fn ($support) => $support->delete());
    }

    public function restored(Project $project): void
    {
        // ✅ Ganti onlyTrashed → withTrashed
        $project->documents()->withTrashed()->get()->each(fn ($document) => $document->restore());
        $project->tasks()->withTrashed()->get()->each(fn ($task) => $task->restore());
        $project->supports()->withTrashed()->get()->each(fn ($support) => $support->restore());
    }
}