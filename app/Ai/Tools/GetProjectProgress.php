<?php

namespace App\Ai\Tools;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectProgress implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan progress sebuah project berdasarkan project_id (persentase, status, penyelesaian tugas, dan aktivitas terbaru). Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        $projectId = (int) ($request['project_id'] ?? 0);

        if ($projectId < 1) {
            return 'Parameter project_id wajib diisi (angka).';
        }

        $project = Project::with([
            'status:id,name',
            'activities' => fn ($query) => $query->with('user:id,name')->latest('activity_date')->limit(10),
        ])->find($projectId);

        if (! $project) {
            return 'Project tidak ditemukan.';
        }

        if (! $this->user->can('view', $project)) {
            return 'Akses ditolak: kamu tidak memiliki izin untuk melihat project ini.';
        }

        $tasks = ProjectTask::query()->where('project_id', $project->id)->get();

        return json_encode([
            'id' => $project->id,
            'project' => $project->project_name,
            'code' => $project->project_code,
            'status' => $project->status?->name,
            'progress' => $project->progress,
            'start_date' => $project->start_date?->toDateString(),
            'end_date' => $project->end_date?->toDateString(),
            'tasks' => [
                'total' => $tasks->count(),
                'done' => $tasks->where('status', 'Done')->count(),
                'in_progress' => $tasks->where('status', 'Progress')->count(),
                'open' => $tasks->where('status', 'Open')->count(),
            ],
            'recent_activities' => $project->activities->map(fn ($activity) => [
                'type' => $activity->type,
                'title' => $activity->title,
                'date' => $activity->activity_date?->toDateString(),
                'user' => $activity->user?->name,
            ])->all(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->required(),
        ];
    }
}
