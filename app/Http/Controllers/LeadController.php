<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectStatus;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
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
        $sources = ['website', 'referral', 'cold_call', 'email', 'social_media', 'event', 'other'];

        return view('leads.index', compact('leads', 'statuses', 'sources'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];
        $sources = ['website', 'referral', 'cold_call', 'email', 'social_media', 'event', 'other'];

        return view('leads.create', compact('customers', 'statuses', 'sources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:new,contacted,qualified,proposal,won,lost',
            'source' => 'nullable|in:website,referral,cold_call,email,social_media,event,other',
            'notes' => 'nullable',
            'opportunity_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Lead::create($validated);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil ditambahkan');
    }

    public function show(Lead $lead)
    {
        $lead->load(['customer', 'assignee', 'customer.projects.documents']);
        $documents = $lead->customer->projects->flatMap->documents;
        $projectStatuses = ProjectStatus::orderBy('sort_order')->get(['id', 'name']);
        $workTypes = WorkType::orderBy('name')->get(['id', 'name']);
        return view('leads.show', compact('lead', 'documents', 'projectStatuses', 'workTypes'));
    }

    public function edit(Lead $lead)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];
        $sources = ['website', 'referral', 'cold_call', 'email', 'social_media', 'event', 'other'];

        return view('leads.edit', compact('lead', 'customers', 'statuses', 'sources'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:new,contacted,qualified,proposal,won,lost',
            'source' => 'nullable|in:website,referral,cold_call,email,social_media,event,other',
            'notes' => 'nullable',
            'opportunity_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($validated);

        return redirect()
            ->route('leads.index')
            ->with('success', 'Lead berhasil diupdate');
    }

    public function destroy(Lead $lead)
    {
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

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Lead berhasil dikonversi ke Project');
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