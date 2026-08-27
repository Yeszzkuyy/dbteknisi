<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadDocument;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    public const SEGMENTS = ['end_user', 'vendor', 'system_integrator', 'kontraktor', 'other'];
    public const SOURCES = ['whatsapp', 'email', 'telpon', 'canvasing', 'event', 'website', 'referral', 'social_media', 'other'];
    public const STATUSES = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];

    public static function label(string $value): string
    {
        return match ($value) {
            'end_user' => 'End User',
            'system_integrator' => 'System Integrator',
            default => ucfirst(str_replace('_', ' ', $value)),
        };
    }

    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'assignee'])
            ->when($request->filled('search'), fn ($q) => $q->whereHas('customer',
                fn ($c) => $c->where('name', 'like', '%'.$request->search.'%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->source))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('incoming_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('incoming_date', '<=', $request->date_to));

        $leads = $query->latest()->paginate(15)->withQueryString();

        $statuses = self::STATUSES;
        $sources = self::SOURCES;

        return view('leads.index', compact('leads', 'statuses', 'sources'));
    }

    public function pipeline()
    {
        $leads = Lead::with(['customer', 'assignee'])->orderByDesc('incoming_date')->get();
        $statuses = self::STATUSES;

        return view('leads.pipeline', compact('leads', 'statuses'));
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', self::STATUSES),
        ]);

        if ($validated['status'] !== $lead->status) {
            $old = $lead->status;
            $lead->update(['status' => $validated['status']]);

            $this->logActivity($lead, 'status_changed', [
                'status' => ['old' => $old, 'new' => $validated['status']],
            ]);
        }

        return response()->noContent();
    }

    public function dashboard()
    {
        $now = now();

        $statusCounts = Lead::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $countMonth = fn ($date) => Lead::whereMonth('incoming_date', $date->month)
            ->whereYear('incoming_date', $date->year)->count();

        $won = (int) ($statusCounts['won'] ?? 0);
        $lost = (int) ($statusCounts['lost'] ?? 0);

        $stats = [
            'total' => array_sum($statusCounts->all()),
            'this_month' => $countMonth($now),
            'last_month' => $countMonth($now->copy()->subMonthNoOverflow()),
            'active' => array_sum($statusCounts->all()) - $won - $lost,
            'won' => $won,
            'lost' => $lost,
            'conversion' => ($won + $lost) > 0 ? (int) round($won / ($won + $lost) * 100) : 0,
        ];

        // ponytail: grouping source di PHP agar portabel antar driver (MySQL/Postgres/SQLite)
        $perSource = Lead::pluck('source')
            ->map(fn ($source) => ($source === null || $source === '') ? 'lainnya' : $source)
            ->countBy()
            ->map(fn ($total, $source) => (object) ['source' => $source, 'total' => $total])
            ->values()
            ->sortByDesc('total')
            ->values();

        // ponytail: tren 6 bulan dikelompokkan di PHP (portabel antar driver DB); pindah ke SQL native kalau datanya jutaan
        $trendQuery = Lead::whereBetween('incoming_date', [
                $now->copy()->subMonths(5)->startOfMonth()->startOfDay(),
                $now->copy()->endOfDay(),
            ])
            ->pluck('incoming_date')
            ->countBy(fn ($date) => $date->format('Y-m'));

        $trend = collect(range(5, 0))->map(function ($i) use ($now, $trendQuery) {
            $month = $now->copy()->subMonthsNoOverflow($i);

            return (object) [
                'label' => $month->translatedFormat('M y'),
                'total' => $trendQuery[sprintf('%04d-%02d', $month->year, $month->month)] ?? 0,
            ];
        });

        return view('marketing.dashboard', compact('stats', 'perSource', 'trend', 'statusCounts'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $segments = self::SEGMENTS;
        $sources = self::SOURCES;
        $ptGroups = Lead::PT_GROUPS;
        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('leads.create', compact('customers', 'segments', 'sources', 'ptGroups', 'salesUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:1000',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'customer_contact_person' => 'nullable|string|max:255',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'assigned_to' => 'required|exists:users,id',
            'segment' => 'required|in:'.implode(',', self::SEGMENTS),
            'source' => 'nullable|in:'.implode(',', self::SOURCES),
            'kebutuhan' => 'nullable|string|max:2000',
            'notes' => 'nullable',
            'incoming_date' => 'required|date',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $validated['customer_id'] = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_company'] ?? $validated['customer_name'],
                'email' => $validated['customer_email'] ?? null,
                'phone' => $validated['customer_phone'] ?? null,
                'address' => $validated['customer_address'] ?? null,
                'contact_person' => $validated['customer_contact_person'] ?? null,
            ])->id;
        } elseif (!empty($validated['customer_contact_person'])) {
            Customer::whereKey($validated['customer_id'])->update([
                'contact_person' => $validated['customer_contact_person'],
            ]);
        }

        unset(
            $validated['customer_mode'],
            $validated['customer_name'],
            $validated['customer_company'],
            $validated['customer_email'],
            $validated['customer_phone'],
            $validated['customer_address'],
            $validated['customer_contact_person'],
        );

        $validated['status'] ??= 'new';

        $lead = Lead::create($validated);
        $this->saveAttachments($request, $lead);
        $this->logActivity($lead, 'created');

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil ditambahkan');
    }

    public function show(Lead $lead)
    {
        $lead->load(['customer', 'activities.user', 'customer.projects.documents']);
        $documents = $lead->customer->projects->flatMap->documents;
        $projectStatuses = ProjectStatus::orderBy('sort_order')->get(['id', 'name']);
        $workTypes = WorkType::orderBy('name')->get(['id', 'name']);
        return view('leads.show', compact('lead', 'documents', 'projectStatuses', 'workTypes'));
    }

    public function edit(Lead $lead)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $segments = self::SEGMENTS;
        $sources = self::SOURCES;
        $ptGroups = Lead::PT_GROUPS;
        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('leads.edit', compact('lead', 'customers', 'segments', 'sources', 'ptGroups', 'salesUsers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:1000',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'customer_contact_person' => 'nullable|string|max:255',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'assigned_to' => 'required|exists:users,id',
            'segment' => 'required|in:'.implode(',', self::SEGMENTS),
            'source' => 'nullable|in:'.implode(',', self::SOURCES),
            'kebutuhan' => 'nullable|string|max:2000',
            'notes' => 'nullable',
            'incoming_date' => 'required|date',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,csv',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $validated['customer_id'] = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_company'] ?? $validated['customer_name'],
                'email' => $validated['customer_email'] ?? null,
                'phone' => $validated['customer_phone'] ?? null,
                'address' => $validated['customer_address'] ?? null,
                'contact_person' => $validated['customer_contact_person'] ?? null,
            ])->id;
        } elseif (!empty($validated['customer_contact_person'])) {
            Customer::whereKey($validated['customer_id'])->update([
                'contact_person' => $validated['customer_contact_person'],
            ]);
        }

        unset(
            $validated['customer_mode'],
            $validated['customer_name'],
            $validated['customer_company'],
            $validated['customer_email'],
            $validated['customer_phone'],
            $validated['customer_address'],
            $validated['customer_contact_person'],
        );

        $lead->fill($validated);

        $changes = [];
        foreach ($validated as $field => $value) {
            if ($lead->isDirty($field)) {
                $changes[$field] = ['old' => $lead->getOriginal($field), 'new' => $value];
            }
        }

        $lead->save();
        $this->syncAttachments($request, $lead);

        if ($changes) {
            $this->logActivity($lead, 'updated', $changes);
        }

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil diupdate');
    }

    public function destroy(Lead $lead)
    {
        $this->logActivity($lead, 'deleted');
        $lead->delete();

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil dihapus');
    }

    public function convert(Lead $lead)
    {
        $this->authorize('manage-marketing');

        $validated = request()->validate([
            'project_name' => 'required|string|max:255',
            'project_status_id' => 'required|exists:project_statuses,id',
            'work_type_id' => 'nullable|exists:work_types,id',
        ]);

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'customer_id' => $lead->customer_id,
            'project_status_id' => $validated['project_status_id'],
            'work_type_id' => $validated['work_type_id'],
            'start_date' => now()->toDateString(),
        ]);

        $lead->update(['status' => 'won']);
        $this->logActivity($lead, 'converted');

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Lead berhasil dikonversi ke Project');
    }

    public function activities(Request $request)
    {
        $activities = LeadActivity::with(['lead.customer', 'user'])
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->user))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $filterUser = $request->filled('user') ? User::find($request->user) : null;

        return view('leads.activities', compact('activities', 'filterUser'))
            ->with('customerNames', Customer::pluck('name', 'id'))
            ->with('userNames', User::pluck('name', 'id'));
    }

    public function monitoring()
    {
        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];

        $groups = collect([
            'Marketing' => User::role('marketing')->orderBy('name')->get(['id', 'name']),
            'Sales' => User::role('sales')->orderBy('name')->get(['id', 'name']),
        ]);

        $ids = $groups->flatten()->pluck('id');

        $counts = Lead::selectRaw('assigned_to, status, count(*) as total')
            ->whereIn('assigned_to', $ids)
            ->groupBy('assigned_to', 'status')
            ->get()
            ->groupBy('assigned_to');

        $lastActivities = LeadActivity::selectRaw('user_id, max(created_at) as last_at')
            ->whereIn('user_id', $ids)
            ->groupBy('user_id')
            ->pluck('last_at', 'user_id')
            ->map(fn ($v) => \Illuminate\Support\Carbon::parse($v));

        $buildRow = fn ($u) => (object) [
            'user' => $u,
            'counts' => collect($statuses)->mapWithKeys(
                fn ($s) => [$s => optional($counts->get($u->id))->firstWhere('status', $s)->total ?? 0]
            ),
            'total' => $counts->get($u->id)?->sum('total') ?? 0,
            'lastActivityAt' => $lastActivities->get($u->id),
        ];

        $summary = $groups->mapWithKeys(
            fn ($users, $division) => [$division => $users->map($buildRow)]
        );

        return view('leads.monitoring', compact('summary', 'statuses'));
    }

    public function showAttachment(Lead $lead, LeadDocument $document)
    {
        if ($document->lead_id !== $lead->id || !Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($document->file_path), [
            'Content-Type' => $document->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
        ]);
    }

    public function downloadAttachment(Lead $lead, LeadDocument $document)
    {
        if ($document->lead_id !== $lead->id) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroyAttachment(Lead $lead, LeadDocument $document)
    {
        if ($document->lead_id !== $lead->id) {
            abort(404);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        $this->logActivity($lead, 'attachment_deleted', [
            'file' => ['old' => $document->file_name, 'new' => null],
        ]);

        return back()->with('success', 'Lampiran berhasil dihapus');
    }

    private function saveAttachments(Request $request, Lead $lead): void
    {
        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store("leads/{$lead->id}", 'public');
            $lead->documents()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
            ]);
        }
    }

    private function syncAttachments(Request $request, Lead $lead): void
    {
        $lead->documents()->delete();

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store("leads/{$lead->id}", 'public');
            $lead->documents()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
            ]);
        }
    }

    private function logActivity(Lead $lead, string $action, ?array $changes = null): void
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }

    public function previewDocument(Lead $lead, ProjectDocument $document)
    {
        $this->authorize('viewAny', Lead::class);

        if ($document->project->customer_id !== $lead->customer_id) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = $document->mime_type ?? mime_content_type($filePath);
        $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));

        $inlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'mp3', 'wav'];

        if (in_array($extension, $inlineTypes)) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }

        $officeTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (in_array($extension, $officeTypes)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function downloadDocument(Lead $lead, ProjectDocument $document)
    {
        $this->authorize('viewAny', Lead::class);

        if ($document->project->customer_id !== $lead->customer_id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}