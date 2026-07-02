<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function deleted(Project $project): void
    {
        $project->documents()->get()->each(fn ($document) => $document->delete());
        $project->tasks()->get()->each(fn ($task) => $task->delete());
        $project->supports()->get()->each(fn ($support) => $support->delete());
    }

    public function restored(Project $project): void
    {
        $project->documents()->onlyTrashed()->get()->each(fn ($document) => $document->restore());
        $project->tasks()->onlyTrashed()->get()->each(fn ($task) => $task->restore());
        $project->supports()->onlyTrashed()->get()->each(fn ($support) => $support->restore());
    }
}