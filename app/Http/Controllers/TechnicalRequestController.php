<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\TechnicalRequest;
use App\Models\User;
use App\Notifications\TechnicalRequestCreatedNotification;
use App\Notifications\TechnicalRequestReviewedNotification;
use App\Notifications\TechnicalWorkCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class TechnicalRequestController extends Controller
{
    private function log(Lead $lead, string $action, array $changes = []): void
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }

    private function authorizeView(TechnicalRequest $request): void
    {
        $user = auth()->user();

        // Alur teknisi + requester sendiri: tanpa LeadPolicy view agar
        // Lead Teknisi bisa memonitor dan teknisi bisa mengerjakan.
        if ($user->can('manage-technician')
            || (int) $request->assigned_technician_id === (int) $user->id
            || (int) $request->requested_by === (int) $user->id) {
            return;
        }

        $this->authorize('view', $request->lead);

        $involved = (int) $request->lead->assigned_to === (int) $user->id;

        abort_unless($involved || $user->can('manage-sales-leads'), 403);
    }

    public function create(Request $request)
    {
        $lead = Lead::findOrFail($request->get('lead_id'));
        $this->authorize('view', $lead);

        return view('sales.technical-requests.create', compact('lead'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'request_type' => 'required|in:' . implode(',', TechnicalRequest::TYPES),
            'title' => 'required|string|max:255',
            'requirement' => 'nullable|string',
            'problem_description' => 'nullable|string',
            'scope' => 'nullable|string',
            'priority' => 'required|in:' . implode(',', TechnicalRequest::PRIORITIES),
            'target_date' => 'nullable|date|after_or_equal:today',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,txt,csv,doc,docx,xls,xlsx',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        $this->authorize('view', $lead);

        $technicalRequest = TechnicalRequest::create([
            ...collect($validated)->except('attachment')->all(),
            'requested_by' => auth()->id(),
            'status' => 'waiting_lead',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->storeAs(
                'tech-requests/' . $technicalRequest->id,
                time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $request->file('attachment')->getClientOriginalName()),
                'private'
            );
            $technicalRequest->update(['attachment_path' => $path]);
        }

        $this->log($lead, 'tech_request_created', ['title' => $technicalRequest->title]);
        $this->log($lead, 'tech_request_sent_to_lead', ['title' => $technicalRequest->title]);

        Notification::send(
            User::role('lead-technician')->get(),
            new TechnicalRequestCreatedNotification($technicalRequest)
        );

        return redirect()->route('sales.technical-requests.show', $technicalRequest)
            ->with('success', __('Technical request dikirim ke Lead Teknisi.'));
    }

    public function show(TechnicalRequest $technicalRequest)
    {
        $technicalRequest->load(['lead.customer', 'requester', 'technician']);
        $this->authorizeView($technicalRequest);

        $technicians = auth()->user()->can('manage-technician')
            ? User::role('technician')->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('sales.technical-requests.show', compact('technicalRequest', 'technicians'));
    }

    public function review(Request $request, TechnicalRequest $technicalRequest)
    {
        abort_unless(auth()->user()->can('manage-technician'), 403);
        $this->authorizeView($technicalRequest);
        abort_unless($technicalRequest->status === 'waiting_lead', 422);

        $validated = $request->validate(['decision' => 'required|in:accepted,rejected']);

        $technicalRequest->update(['status' => $validated['decision']]);
        $this->log($technicalRequest->lead, 'tech_request_' . $validated['decision'], ['title' => $technicalRequest->title]);
        $technicalRequest->requester?->notify(new TechnicalRequestReviewedNotification($technicalRequest, $validated['decision']));

        return back()->with('success', __('Request telah di-' . $validated['decision'] . '.'));
    }

    public function assign(Request $request, TechnicalRequest $technicalRequest)
    {
        abort_unless(auth()->user()->can('manage-technician'), 403);
        $this->authorizeView($technicalRequest);
        abort_unless(in_array($technicalRequest->status, ['accepted', 'assigned']), 422);

        $validated = $request->validate(['technician_id' => 'required|exists:users,id']);

        $technician = User::findOrFail($validated['technician_id']);
        abort_unless($technician->hasRole('technician'), 422, __('Hanya user role Teknisi yang bisa di-assign.'));

        $technicalRequest->update([
            'assigned_technician_id' => $technician->id,
            'status' => 'assigned',
        ]);

        $this->log($technicalRequest->lead, 'technician_assigned', [
            'title' => $technicalRequest->title,
            'technician' => $technician->name,
        ]);
        $technicalRequest->requester?->notify(new TechnicalRequestReviewedNotification($technicalRequest, 'assigned'));

        return back()->with('success', __('Teknisi berhasil di-assign.'));
    }

    public function progress(Request $request, TechnicalRequest $technicalRequest)
    {
        $this->authorizeWork($technicalRequest);
        abort_unless(in_array($technicalRequest->status, ['assigned', 'in_progress']), 422);

        $validated = $request->validate(['notes' => 'nullable|string|max:2000']);

        $technicalRequest->update([
            'status' => 'in_progress',
            'notes' => $validated['notes'] ?? $technicalRequest->notes,
        ]);

        return back()->with('success', __('Progress diperbarui.'));
    }

    public function result(Request $request, TechnicalRequest $technicalRequest)
    {
        $this->authorizeWork($technicalRequest);
        abort_unless(in_array($technicalRequest->status, ['assigned', 'in_progress']), 422);

        // Hasil teknis hanya bisa diisi pelaksana (teknisi yang di-assign) — Sales dilarang.
        $validated = $request->validate(['technical_result' => 'required|string']);

        $technicalRequest->update(['technical_result' => $validated['technical_result']]);
        $this->log($technicalRequest->lead, 'technical_result_submitted', ['title' => $technicalRequest->title]);

        return back()->with('success', __('Hasil teknis tersimpan.'));
    }

    public function complete(TechnicalRequest $technicalRequest)
    {
        $user = auth()->user();
        abort_unless(
            (int) $technicalRequest->assigned_technician_id === (int) $user->id
                || $user->can('manage-technician'),
            403
        );
        $this->authorizeView($technicalRequest);
        abort_unless(in_array($technicalRequest->status, ['assigned', 'in_progress']), 422);
        abort_unless(filled($technicalRequest->technical_result), 422, __('Isi hasil teknis dulu sebelum menyelesaikan.'));

        $technicalRequest->update(['status' => 'completed', 'completed_at' => now()]);
        $this->log($technicalRequest->lead, 'tech_request_completed', ['title' => $technicalRequest->title]);
        $technicalRequest->requester?->notify(new TechnicalWorkCompletedNotification($technicalRequest));

        return back()->with('success', __('Technical request selesai.'));
    }

    public function cancel(TechnicalRequest $technicalRequest)
    {
        $user = auth()->user();
        abort_unless(
            (int) $technicalRequest->requested_by === (int) $user->id
                || $user->can('manage-sales-leads'),
            403
        );
        $this->authorizeView($technicalRequest);
        abort_unless(! in_array($technicalRequest->status, ['completed', 'cancelled']), 422);

        $technicalRequest->update(['status' => 'cancelled']);
        $this->log($technicalRequest->lead, 'tech_request_cancelled', ['title' => $technicalRequest->title]);

        return back()->with('success', __('Technical request dibatalkan.'));
    }

    public function downloadAttachment(TechnicalRequest $technicalRequest)
    {
        $this->authorizeView($technicalRequest);
        abort_unless($technicalRequest->attachment_path && Storage::disk('private')->exists($technicalRequest->attachment_path), 404);

        return Storage::disk('private')->download($technicalRequest->attachment_path);
    }

    private function authorizeWork(TechnicalRequest $technicalRequest): void
    {
        $this->authorizeView($technicalRequest);
        // Sales (termasuk requester) dilarang menyentuh progres/hasil teknis.
        abort_unless(
            (int) $technicalRequest->assigned_technician_id === (int) auth()->id(),
            403
        );
    }
}
