<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadDocument;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadController extends Controller
{
    public const SEGMENTS = ['end_user', 'vendor', 'system_integrator', 'kontraktor', 'gov', 'principle', 'distributor', 'other'];
    public const SOURCES = ['whatsapp', 'email', 'telpon', 'canvasing', 'event', 'website', 'referral', 'social_media', 'other'];
    public const STATUSES = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];

    public static function label(string $value): string
    {
        return match ($value) {
            'end_user' => 'End User',
            'system_integrator' => 'System Integrator',
            'gov' => 'Gov',
            'principle' => 'Principle',
            'distributor' => 'Distributor',
            default => ucfirst(str_replace('_', ' ', $value)),
        };
    }

    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'assignee', 'partner'])
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
        $leads = Lead::with(['customer', 'assignee', 'partner'])->orderByDesc('incoming_date')->get();
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

    public function batchUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'changes' => 'required|array',
            'changes.*.lead_id' => 'required|exists:leads,id',
            'changes.*.status' => 'required|in:'.implode(',', self::STATUSES),
        ]);

        $results = [];
        foreach ($validated['changes'] as $change) {
            $lead = Lead::find($change['lead_id']);
            $old = $lead->status;

            if ($old !== $change['status']) {
                $lead->update(['status' => $change['status']]);

                $this->logActivity($lead, 'status_changed', [
                    'status' => ['old' => $old, 'new' => $change['status']],
                ]);

                $results[] = ['lead_id' => $lead->id, 'old' => $old, 'new' => $change['status']];
            }
        }

        return response()->json(['updated' => count($results), 'changes' => $results]);
    }

    public function dashboard(Request $request)
    {
        $now = now();
        $dateFrom = $request->filled('date_from') ? $request->date_from : $now->copy()->subMonths(5)->startOfMonth()->toDateString();
        $dateTo = $request->filled('date_to') ? $request->date_to : $now->toDateString();

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        // whereDate di kedua batas agar lead hari ini ikut terhitung (cast 'date' menyimpan jam juga)
        $statusCounts = Lead::whereDate('incoming_date', '>=', $dateFrom)->whereDate('incoming_date', '<=', $dateTo)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $countMonth = fn ($date) => Lead::whereDate('incoming_date', '>=', $dateFrom)->whereDate('incoming_date', '<=', $dateTo)
            ->whereMonth('incoming_date', $date->month)
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
        $perSource = Lead::whereDate('incoming_date', '>=', $dateFrom)->whereDate('incoming_date', '<=', $dateTo)
            ->pluck('source')
            ->map(fn ($source) => ($source === null || $source === '') ? 'lainnya' : $source)
            ->countBy()
            ->map(fn ($total, $source) => (object) ['source' => $source, 'total' => $total])
            ->values()
            ->sortByDesc('total')
            ->values();

        // ponytail: tren per bulan dikelompokkan di PHP (portabel antar driver DB); pindah ke SQL native kalau datanya jutaan
        $trendQuery = Lead::whereDate('incoming_date', '>=', $dateFrom)->whereDate('incoming_date', '<=', $dateTo)
            ->pluck('incoming_date')
            ->countBy(fn ($date) => $date->format('Y-m'));

        $months = max(0, now()->parse($dateFrom)->diffInMonths(now()->parse($dateTo)));
        $trend = $months < 1
            ? collect([(object) [
                'label' => now()->parse($dateTo)->translatedFormat('M y'),
                'total' => $trendQuery[now()->parse($dateTo)->format('Y-m')] ?? 0,
            ]])
            : collect(range(0, $months))->map(function ($i) use ($dateTo, $trendQuery) {
                $month = now()->parse($dateTo)->subMonthsNoOverflow($i);

                return (object) [
                    'label' => $month->translatedFormat('M y'),
                    'total' => $trendQuery[sprintf('%04d-%02d', $month->year, $month->month)] ?? 0,
                ];
            })->reverse()->values();

        // Funnel: jumlah lead per status, diurutkan sesuai STATUSES (New → Lost)
        $funnel = collect(self::STATUSES)->map(fn ($status) => [
            'label' => ucfirst($status),
            'value' => (int) ($statusCounts[$status] ?? 0),
            'key'   => $status,
        ]);

        return view('marketing.dashboard', compact('stats', 'perSource', 'trend', 'statusCounts', 'dateFrom', 'dateTo', 'funnel'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $partners = Partner::orderBy('name')->get(['id', 'name', 'type']);
        $segments = self::SEGMENTS;
        $sources = self::SOURCES;
        $ptGroups = Lead::PT_GROUPS;
        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('leads.create', compact('customers', 'partners', 'segments', 'sources', 'ptGroups', 'salesUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:1000',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'customer_contact_person' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'assigned_to' => 'required|exists:users,id',
            'segment' => 'required|in:'.implode(',', self::SEGMENTS),
            'source' => 'nullable|in:'.implode(',', self::SOURCES),
            'kebutuhan' => 'nullable|string|max:2000',
            'solusi' => 'nullable|string|max:2000',
            'progress_notes' => 'nullable|string|max:2000',
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
                'whatsapp' => $validated['customer_whatsapp'] ?? null,
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
            $validated['customer_whatsapp'],
            $validated['customer_address'],
            $validated['customer_contact_person'],
            $validated['attachments'],
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
        $lead->load(['customer', 'partner', 'activities.user', 'customer.projects.documents']);
        $documents = $lead->customer->projects->flatMap->documents;
        $projectStatuses = ProjectStatus::orderBy('sort_order')->get(['id', 'name']);
        $workTypes = WorkType::orderBy('name')->get(['id', 'name']);
        return view('leads.show', compact('lead', 'documents', 'projectStatuses', 'workTypes'));
    }

    public function edit(Lead $lead)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $partners = Partner::orderBy('name')->get(['id', 'name', 'type']);
        $segments = self::SEGMENTS;
        $sources = self::SOURCES;
        $ptGroups = Lead::PT_GROUPS;
        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('leads.edit', compact('lead', 'customers', 'partners', 'segments', 'sources', 'ptGroups', 'salesUsers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:1000',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'customer_contact_person' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id',
            'pt_group' => 'required|in:'.implode(',', Lead::PT_GROUPS),
            'assigned_to' => 'required|exists:users,id',
            'segment' => 'required|in:'.implode(',', self::SEGMENTS),
            'source' => 'nullable|in:'.implode(',', self::SOURCES),
            'kebutuhan' => 'nullable|string|max:2000',
            'solusi' => 'nullable|string|max:2000',
            'progress_notes' => 'nullable|string|max:2000',
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
                'whatsapp' => $validated['customer_whatsapp'] ?? null,
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
            $validated['customer_whatsapp'],
            $validated['customer_address'],
            $validated['customer_contact_person'],
            $validated['attachments'],
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

    public function importForm()
    {
        return view('leads.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            } else {
                $rows = [];
                $handle = fopen($file->getRealPath(), 'r');
                while (($row = fgetcsv($handle, 0, ',')) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }

            if (count($rows) < 2) {
                return back()->withErrors(['file' => 'File kosong atau hanya berisi header.']);
            }

            $headers = array_map(fn ($h) => strtolower(trim(str_replace([' ', "\t"], '_', $h))), $rows[0]);
            $results = ['success' => 0, 'failed' => 0, 'errors' => []];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row)) || count($row) < count($headers)) continue;

                $data = array_combine($headers, $row);
                $rowNum = $i + 1;

                try {
                    $companyName = $data['nama_perusahaan'] ?? $data['company'] ?? $data['perusahaan'] ?? null;
                    if (empty($companyName)) {
                        throw new \Exception('Nama perusahaan kosong');
                    }

                    $customer = Customer::firstOrCreate(
                        ['name' => trim($companyName)],
                        [
                            'company' => trim($companyName),
                            'address' => $data['alamat'] ?? $data['address'] ?? null,
                            'phone' => $data['telp'] ?? $data['phone'] ?? $data['telepon'] ?? null,
                            'whatsapp' => $data['wa'] ?? $data['whatsapp'] ?? null,
                            'email' => $data['email'] ?? null,
                            'contact_person' => $data['pic'] ?? $data['contact_person'] ?? null,
                        ]
                    );

                    $incomingDate = $data['tanggal'] ?? $data['date'] ?? $data['incoming_date'] ?? now()->toDateString();
                    if (!strtotime($incomingDate)) $incomingDate = now()->toDateString();

                    $assignedTo = null;
                    $salesVal = $data['sales'] ?? $data['assigned_to'] ?? null;
                    if ($salesVal) {
                        $salesUser = User::where('name', 'like', "%{$salesVal}%")->first()
                            ?? User::find($salesVal);
                        $assignedTo = $salesUser?->id;
                    }

                    Lead::create([
                        'customer_id' => $customer->id,
                        'pt_group' => $data['pt'] ?? $data['pt_group'] ?? 'NTI',
                        'segment' => $data['segment'] ?? 'other',
                        'source' => $data['source'] ?? $data['masuk_by'] ?? null,
                        'kebutuhan' => $data['kebutuhan'] ?? null,
                        'solusi' => $data['solusi'] ?? null,
                        'progress_notes' => $data['progress'] ?? $data['followup'] ?? null,
                        'notes' => $data['catatan'] ?? $data['notes'] ?? null,
                        'incoming_date' => $incomingDate,
                        'assigned_to' => $assignedTo,
                        'status' => 'new',
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Baris {$rowNum}: {$e->getMessage()}";
                }
            }

            return back()->with('import_results', $results);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }
}