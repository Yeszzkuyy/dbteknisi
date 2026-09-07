<?php

namespace App\Ai\Tools;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetLeadDetails implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan detail sebuah lead berdasarkan lead_id, atau (tanpa lead_id) ringkasan lead yang dapat diakses user: jumlah per status dan daftar lead. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        $canMarketing = $this->user->hasAnyPermission(['view-marketing', 'manage-marketing']);
        $canSales = $this->user->hasAnyPermission(['view-sales', 'manage-sales']);

        if (! $canMarketing && ! $canSales) {
            return 'Akses ditolak: kamu tidak memiliki izin untuk melihat data lead.';
        }

        $scopedQuery = Lead::query();

        if ($canSales && ! $canMarketing) {
            $scopedQuery->where('assigned_to', $this->user->id);
        }

        $leadId = (int) ($request['lead_id'] ?? 0);

        if ($leadId < 1) {
            $counts = (clone $scopedQuery)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $recent = (clone $scopedQuery)
                ->with('customer:id,name,company')
                ->latest('incoming_date')
                ->limit(10)
                ->get();

            return json_encode([
                'total' => $counts->sum(),
                'by_status' => $counts->toArray(),
                'recent' => $recent->map(fn ($lead) => [
                    'id' => $lead->id,
                    'customer' => $lead->customer?->name,
                    'company' => $lead->customer?->company,
                    'status' => $lead->status,
                    'assigned_to' => $lead->assigned_to,
                ])->all(),
            ], JSON_UNESCAPED_UNICODE);
        }

        $lead = (clone $scopedQuery)
            ->with([
                'customer:id,name,company',
                'assignee:id,name',
                'activities' => fn ($query) => $query->latest('activity_date')->limit(10),
            ])
            ->find($leadId);

        if (! $lead) {
            return 'Lead tidak ditemukan atau kamu tidak berizin mengaksesnya.';
        }

        return json_encode([
            'id' => $lead->id,
            'customer' => $lead->customer?->name,
            'company' => $lead->customer?->company,
            'pt_group' => $lead->pt_group,
            'segment' => $lead->segment,
            'status' => $lead->status,
            'source' => $lead->source,
            'kebutuhan' => str($lead->kebutuhan)->limit(300)->toString(),
            'solusi' => str($lead->solusi)->limit(300)->toString(),
            'progress_notes' => str($lead->progress_notes)->limit(300)->toString(),
            'notes' => str($lead->notes)->limit(300)->toString(),
            'assignee' => $lead->assignee?->name,
            'incoming_date' => $lead->incoming_date?->toDateString(),
            'recent_activities' => $lead->activities->map(fn ($activity) => [
                'type' => $activity->type,
                'title' => $activity->title,
                'date' => $activity->activity_date?->toDateString(),
            ])->all(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'lead_id' => $schema->integer()->nullable(),
        ];
    }
}
