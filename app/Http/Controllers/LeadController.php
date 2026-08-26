<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
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
        $query = Lead::with(['customer', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];
        $sources = self::SOURCES;

        return view('leads.index', compact('leads', 'statuses', 'sources'));
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
        $juniors = User::role('marketing')
            ->orderBy('name')
            ->get(['id', 'name']);

        $counts = Lead::selectRaw('assigned_to, status, count(*) as total')
            ->whereIn('assigned_to', $juniors->pluck('id'))
            ->groupBy('assigned_to', 'status')
            ->get()
            ->groupBy('assigned_to');

        $lastActivities = LeadActivity::selectRaw('user_id, max(created_at) as last_at')
            ->whereIn('user_id', $juniors->pluck('id'))
            ->groupBy('user_id')
            ->pluck('last_at', 'user_id')
            ->map(fn ($v) => \Illuminate\Support\Carbon::parse($v));

        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];

        $summary = $juniors->map(fn ($u) => (object) [
            'user' => $u,
            'counts' => collect($statuses)->mapWithKeys(
                fn ($s) => [$s => optional($counts->get($u->id))->firstWhere('status', $s)->total ?? 0]
            ),
            'total' => $counts->get($u->id)?->sum('total') ?? 0,
            'lastActivityAt' => $lastActivities->get($u->id),
        ]);

        return view('leads.monitoring', compact('summary', 'statuses'));
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