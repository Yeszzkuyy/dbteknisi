<?php

namespace App\Ai\Tools;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetLatestProjects implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan daftar project internal terbaru (maksimal 10). Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if (! $this->user->hasAnyPermission(['view-teknisi', 'manage-teknisi', 'view-sales'])) {
            return 'Akses ditolak: kamu tidak memiliki izin untuk melihat data project.';
        }

        $projects = Project::query()
            ->with(['customer:id,name,company', 'status:id,name'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        if ($projects->isEmpty()) {
            return 'Belum ada project terdaftar.';
        }

        return json_encode(
            $projects->map(fn ($project) => [
                'id' => $project->id,
                'project' => $project->project_name,
                'code' => $project->project_code,
                'customer' => $project->customer?->name,
                'status' => $project->status?->name,
                'progress' => $project->progress,
                'start_date' => $project->start_date?->toDateString(),
                'end_date' => $project->end_date?->toDateString(),
            ])->all(),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
