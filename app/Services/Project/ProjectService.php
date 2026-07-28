<?php

namespace App\Services\Project;

use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function create(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([
                'customer_id' => $data['customer_id'],
                'project_name' => $data['name'] ?? null,
                'work_type_id' => $data['work_type_id'] ?? null,
                'account_manager_id' => $data['account_manager_id'] ?? null,
                'pic_engineer' => $data['pic_engineer'] ?? null, // ← text
                'support_technicians' => $data['support_technicians'] ?? null, // ← text
                'description' => $data['description'] ?? null,
                'status' => 'Open',
                'progress' => 0,
            ]);

            ProjectActivity::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'activity_date' => now(),
                'title' => 'Project Dibuat',
                'description' => 'Project baru berhasil dibuat',
            ]);

            return $project;
        });
    }

    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'project_name' => $data['project_name'] ?? $project->project_name,
                'customer_id' => $data['customer_id'] ?? $project->customer_id,
                'work_type_id' => $data['work_type_id'] ?? $project->work_type_id,
                'account_manager_id' => $data['account_manager_id'] ?? $project->account_manager_id,
                'pic_engineer' => $data['pic_engineer'] ?? $project->pic_engineer,
                'support_technicians' => $data['support_technicians'] ?? $project->support_technicians,
                'status' => $data['status'] ?? $project->status,
                'progress' => $data['progress'] ?? $project->progress,
                'description' => $data['description'] ?? $project->description,
            ]);

            return $project;
        });
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }
}