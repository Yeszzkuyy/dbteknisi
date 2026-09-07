<?php

namespace App\Ai\Tools;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetSalesSummary implements Tool
{
    public function __construct(public User $user) {}

    public function description(): Stringable|string
    {
        return 'Menampilkan ringkasan penjualan internal: jumlah lead, customer, project, invoice, pembayaran, dan purchase order. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if (! $this->user->hasAnyPermission(['view-monitoring', 'view-admin'])) {
            return 'Akses ditolak: ringkasan penjualan hanya tersedia untuk user dengan izin monitoring atau admin.';
        }

        $leadCounts = Lead::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $invoiceTotals = Invoice::query()
            ->select(DB::raw('count(*) as total'), DB::raw('coalesce(sum(amount), 0) as amount'))
            ->first();

        $paidTotal = Payment::query()->sum('amount');
        $outstanding = ($invoiceTotals?->amount ?? 0) - $paidTotal;

        $poTotals = PurchaseOrder::query()
            ->select(DB::raw('count(*) as total'), DB::raw('coalesce(sum(amount), 0) as amount'))
            ->first();

        return json_encode([
            'leads' => [
                'total' => $leadCounts->sum(),
                'by_status' => $leadCounts->toArray(),
            ],
            'customers' => Customer::query()->count(),
            'projects' => [
                'total' => Project::query()->count(),
                'active' => Project::whereHas('status', fn ($query) => $query->whereIn('name', [
                    ProjectStatus::Open->value,
                    ProjectStatus::OnProgress->value,
                ]))->count(),
            ],
            'invoices' => [
                'total' => (int) ($invoiceTotals?->total ?? 0),
                'total_amount' => (float) ($invoiceTotals?->amount ?? 0),
                'paid' => (float) $paidTotal,
                'outstanding' => (float) $outstanding,
            ],
            'purchase_orders' => [
                'total' => (int) ($poTotals?->total ?? 0),
                'total_amount' => (float) ($poTotals?->amount ?? 0),
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
