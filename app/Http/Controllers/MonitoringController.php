<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\FollowUp;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\ProjectDocument;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-monitoring');

        // Summary Stats
        $stats = $this->getStats($request);

        // Customer Progress List
        $customers = $this->getCustomerProgress($request);

        // Filter options
        $divisiOptions = ['marketing', 'sales', 'admin', 'teknisi'];
        $statusOptions = $this->getAllStatuses();

        return view('monitoring.index', compact(
            'stats',
            'customers',
            'divisiOptions',
            'statusOptions'
        ));
    }

    private function getStats(Request $request): array
    {
        // Lead masuk (Marketing)
        $leadQuery = Lead::query();
        if ($request->filled('date_from')) {
            $leadQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $leadQuery->whereDate('created_at', '<=', $request->date_to);
        }
        $leadMasuk = $leadQuery->count();

        // Meeting bulan ini (Sales)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $meetingQuery = Meeting::whereBetween('meeting_date', [$startOfMonth, $endOfMonth]);
        if ($request->filled('date_from')) {
            $meetingQuery->whereDate('meeting_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $meetingQuery->whereDate('meeting_date', '<=', $request->date_to);
        }
        $meetingBulanIni = $meetingQuery->count();

        // Invoice Outstanding (Admin) - unpaid invoices
        $invoiceQuery = Invoice::where('status', 'unpaid');
        if ($request->filled('date_from')) {
            $invoiceQuery->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $invoiceQuery->whereDate('issue_date', '<=', $request->date_to);
        }
        $invoiceOutstanding = $invoiceQuery->sum('amount');

        // Instalasi Proses (Teknisi) - projects with status Open/Progress
        $instalasiProsesQuery = Project::whereHas('status', fn ($q) => $q->whereIn('name', [
            ProjectStatus::Open->value,
            ProjectStatus::OnProgress->value,
        ]));
        if ($request->filled('date_from')) {
            $instalasiProsesQuery->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $instalasiProsesQuery->whereDate('start_date', '<=', $request->date_to);
        }
        $instalasiProses = $instalasiProsesQuery->count();

        // Instalasi Selesai (Teknisi) - projects with status Done
        $instalasiSelesaiQuery = Project::whereHas('status', fn ($q) => $q->where('name', 'Done'));
        if ($request->filled('date_from')) {
            $instalasiSelesaiQuery->whereDate('end_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $instalasiSelesaiQuery->whereDate('end_date', '<=', $request->date_to);
        }
        $instalasiSelesai = $instalasiSelesaiQuery->count();

        // PO Menunggu Proses (Admin) - PO with status draft/diproses
        $poQuery = PurchaseOrder::whereIn('status', ['draft', 'diproses']);
        if ($request->filled('date_from')) {
            $poQuery->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $poQuery->whereDate('issue_date', '<=', $request->date_to);
        }
        $poMenunggu = $poQuery->count();

        return [
            'lead_masuk' => $leadMasuk,
            'meeting_bulan_ini' => $meetingBulanIni,
            'invoice_outstanding' => $invoiceOutstanding,
            'instalasi_proses' => $instalasiProses,
            'instalasi_selesai' => $instalasiSelesai,
            'po_menunggu' => $poMenunggu,
        ];
    }

    private function getCustomerProgress(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Customer::with([
            'leads' => fn($q) => $q->latest('created_at'),
            'meetings' => fn($q) => $q->latest('meeting_date'),
            'followUps' => fn($q) => $q->latest('follow_up_date'),
            'projects' => fn($q) => $q->with(['documents', 'status'])->latest('start_date'),
            'invoices' => fn($q) => $q->latest('issue_date'),
            'purchaseOrders' => fn($q) => $q->latest('issue_date'),
        ]);

        // Search customer
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('company', 'like', '%' . $request->search . '%');
        }

        // Filter by divisi (latest update source)
        if ($request->filled('divisi')) {
            // We'll filter in the collection since it's complex
        }

        $customers = $query->get();

        // Add computed properties to each customer
        $customers->transform(function ($customer) {
            $customer->latest_activity = $this->getLatestActivity($customer);
            $customer->overall_status = $this->getOverallStatus($customer);
            $customer->latest_divisi = $this->getLatestDivisi($customer);
            return $customer;
        });

        // ponytail: sort di PHP setelah full load; pindah ke subquery SQL kalau customer sudah ribuan
        $customers = $customers
            ->sortByDesc(fn ($c) => $c->latest_activity['date'] ?? null)
            ->values();

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $customers->forPage($page, 15),
            $customers->count(),
            15,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }

    private function getLatestActivity(Customer $customer): ?array
    {
        $activities = collect();

        // Lead
        foreach ($customer->leads as $lead) {
            $activities->push([
                'type' => 'lead',
                'label' => 'Lead: ' . ucfirst($lead->status),
                'overall' => 'Lead: ' . ucfirst($lead->status),
                'date' => $lead->created_at,
                'divisi' => 'marketing',
                'icon' => 'briefcase',
                'color' => 'blue',
                'url' => route('leads.show', $lead),
            ]);
        }

        // Meeting
        foreach ($customer->meetings as $meeting) {
            $activities->push([
                'type' => 'meeting',
                'label' => 'Meeting',
                'overall' => 'Meeting',
                'date' => $meeting->meeting_date,
                'divisi' => 'sales',
                'icon' => 'calendar',
                'color' => 'green',
                'url' => route('sales.meetings.show', $meeting),
            ]);
        }

        // Follow Up
        foreach ($customer->followUps as $followUp) {
            $activities->push([
                'type' => 'followup',
                'label' => 'Follow Up',
                'overall' => 'Follow Up',
                'date' => $followUp->follow_up_date,
                'divisi' => 'sales',
                'icon' => 'arrow-path',
                'color' => 'emerald',
                'url' => route('sales.follow-ups.show', $followUp),
            ]);
        }

        $projects = $customer->projects;
        foreach ($projects as $project) {
            $statusName = $project->status?->name ?? 'Belum Memulai';
            $activities->push([
                'type' => 'project',
                'label' => 'Project: ' . $statusName,
                'overall' => $statusName,
                'date' => $project->start_date,
                'divisi' => 'teknisi',
                'icon' => 'folder',
                'color' => 'purple',
                'url' => route('projects.show', $project),
            ]);
        }

        // Invoice
        foreach ($customer->invoices as $invoice) {
            $activities->push([
                'type' => 'invoice',
                'label' => 'Invoice: ' . ucfirst($invoice->status),
                'overall' => 'Invoice: ' . ucfirst($invoice->status),
                'date' => $invoice->issue_date,
                'divisi' => 'admin',
                'icon' => 'document-text',
                'color' => 'orange',
                'url' => route('admin.invoices.show', $invoice),
            ]);
        }

        // PO
        foreach ($customer->purchaseOrders as $po) {
            $activities->push([
                'type' => 'po',
                'label' => 'PO: ' . ucfirst($po->status),
                'overall' => 'PO: ' . ucfirst($po->status),
                'date' => $po->issue_date,
                'divisi' => 'admin',
                'icon' => 'clipboard-document',
                'color' => 'amber',
                'url' => route('admin.pos.show', $po),
            ]);
        }

        $latest = $activities->sortByDesc('date')->first();
        return $latest;
    }

    private function getOverallStatus(Customer $customer): string
    {
        // Status mengikuti aktivitas terbaru apa pun divisinya,
        // supaya konsisten dengan kolom Divisi Terakhir Update & Waktu.
        return $this->getLatestActivity($customer)['overall'] ?? 'Baru';
    }

    private function getLatestDivisi(Customer $customer): ?string
    {
        $latest = $this->getLatestActivity($customer);
        return $latest['divisi'] ?? null;
    }

    private function getAllStatuses(): array
    {
        return [
            'marketing' => ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'],
            'sales' => ['meeting', 'followup'],
            'teknisi' => \App\Models\ProjectStatus::orderBy('sort_order')->pluck('name')->all(),
            'admin' => ['unpaid', 'paid', 'cancelled', 'draft', 'diproses', 'selesai', 'dibatalkan'],
        ];
    }
}