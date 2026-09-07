<?php

namespace App\Ai\Tools;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectDetails implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan detail sebuah project berdasarkan project_id (informasi umum, customer, dan daftar tugas). Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        $projectId = (int) ($request['project_id'] ?? 0);

        if ($projectId < 1) {
            return 'Parameter project_id wajib diisi (angka).';
        }

        $project = Project::with([
            'customer:id,name,company',
            'status:id,name',
            'workType:id,name',
            'tasks' => fn ($query) => $query->limit(20),
        ])->find($projectId);

        if (! $project) {
            return 'Project tidak ditemukan.';
        }

        if (! $this->user->can('view', $project)) {
            return 'Akses ditolak: kamu tidak memiliki izin untuk melihat project ini.';
        }

        return json_encode([
            'id' => $project->id,
            'project' => $project->project_name,
            'code' => $project->project_code,
            'quotation_number' => $project->quotation_number,
            'status' => $project->status?->name,
            'progress' => $project->progress,
            'customer' => $project->customer?->name,
            'company' => $project->customer?->company,
            'work_type' => $project->workType?->name,
            'pic_engineer' => $project->pic_engineer,
            'start_date' => $project->start_date?->toDateString(),
            'end_date' => $project->end_date?->toDateString(),
            'description' => str($project->description)->limit(300)->toString(),
            'tasks' => $project->tasks->map(fn ($task) => [
                'id' => $task->id,
                'task' => $task->title,
                'status' => $task->status,
                'assigned_to' => $task->assigned_to,
                'due_date' => $task->due_date?->toDateString(),
            ])->all(),
            'document_count' => $project->documents()->count(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->required(),
        ];
    }
}
