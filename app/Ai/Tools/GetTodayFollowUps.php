<?php

namespace App\Ai\Tools;

use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetTodayFollowUps implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan follow-up milik user hari ini (atau customer tertentu bila customer_id diisi) sebagai konteks penyusunan update meeting. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = FollowUp::with(['customer:id,name', 'lead:id,customer_id,status'])
            ->whereDate('created_at', today())
            ->where(function ($q) {
                $q->where('created_by', $this->user->id)
                    ->orWhereHas('lead', fn ($l) => $l->where('assigned_to', $this->user->id));
            })
            ->orderBy('created_at');

        if ($customerId = (int) ($request['customer_id'] ?? 0)) {
            $query->where('customer_id', $customerId);
        }

        $items = $query->limit(10)->get();

        if ($items->isEmpty()) {
            return 'Tidak ada follow-up hari ini.';
        }

        return json_encode($items->map(fn (FollowUp $fu) => [
            'customer' => $fu->customer?->name,
            'lead_status' => $fu->lead?->status,
            'type' => $fu->type,
            'description' => str($fu->description)->limit(300)->toString(),
            'follow_up_date' => $fu->follow_up_date?->toDateString(),
            'completed' => (bool) $fu->completed_at,
        ])->all(), JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()->nullable(),
        ];
    }
}
