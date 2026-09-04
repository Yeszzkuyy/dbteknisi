<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class ManageSalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'assignee', 'partner'])
            ->when($request->filled('search'), fn ($q) => $q->whereHas('customer',
                fn ($c) => $c->where('name', 'like', '%'.$request->search.'%')))
            ->when($request->filled('assignment'), fn ($q) => $request->assignment === 'assigned'
                ? $q->whereNotNull('assigned_to')
                : $q->whereNull('assigned_to'));

        $leads = $query->latest()->paginate(15)->withQueryString();

        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('manage-sales.index', compact('leads', 'salesUsers'));
    }

    public function edit(Lead $lead)
    {
        $lead->load(['customer', 'assignee']);

        $salesUsers = User::role('sales')->orderBy('name')->get(['id', 'name']);

        return view('manage-sales.edit', compact('lead', 'salesUsers'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'kebutuhan' => 'nullable|string|max:2000',
            'solusi' => 'nullable|string|max:2000',
            'progress_notes' => 'nullable|string|max:2000',
            'notes' => 'nullable',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if (!empty($validated['assigned_to'])) {
            $salesUser = User::find($validated['assigned_to']);
            if (!$salesUser?->hasRole('sales')) {
                abort(422, 'Target assignment harus user dengan role Sales.');
            }
        }

        $lead->fill($validated);
        $this->trackAssignment($lead, $validated['assigned_to'] ?? null);

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

        if (!empty($validated['assigned_to'])) {
            $this->clearLeadNotifications($lead);
        }

        return redirect()
            ->route('manage-sales.index')
            ->with('success', 'Lead berhasil diperbarui');
    }

    public function assign(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $salesUser = User::find($validated['assigned_to']);
        if (!$salesUser?->hasRole('sales')) {
            abort(422, 'Target assignment harus user dengan role Sales.');
        }

        $lead->assigned_to = $salesUser->id;
        $this->trackAssignment($lead, $salesUser->id);
        $lead->save();

        $this->logActivity($lead, 'assigned', [
            'assigned_to' => ['old' => $lead->getOriginal('assigned_to'), 'new' => $salesUser->id],
        ]);

        $this->clearLeadNotifications($lead);

        return redirect()
            ->route('manage-sales.index')
            ->with('success', "Lead di-assign ke {$salesUser->name}");
    }

    public function myLeads()
    {
        $leads = Lead::with(['customer', 'partner'])
            ->where('assigned_to', auth()->id())
            ->latest()
            ->paginate(15);

        return view('sales.my-leads', compact('leads'));
    }

    public function activityLog(Request $request)
    {
        $managementIds = User::permission('manage-sales-leads')->pluck('id');

        $activities = LeadActivity::with(['lead.customer', 'user'])
            ->whereIn('user_id', $managementIds)
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->user))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $filterUser = $request->filled('user') ? User::find($request->user) : null;

        return view('manage-sales.activity-log', compact('activities', 'filterUser'))
            ->with('customerNames', Customer::pluck('name', 'id'))
            ->with('userNames', User::pluck('name', 'id'))
            ->with('managementUsers', User::permission('manage-sales-leads')->orderBy('name')->get(['id', 'name']));
    }

    private function trackAssignment(Lead $lead, ?int $assignedTo): void
    {
        $lead->assigned_to = $assignedTo;
        $lead->assigned_by = $assignedTo ? auth()->id() : null;
        $lead->assigned_at = $assignedTo ? now() : null;
    }

    private function clearLeadNotifications(Lead $lead): void
    {
        DatabaseNotification::where('type', NewLeadNotification::class)
            ->get()
            ->filter(fn ($n) => ($n->data['lead_id'] ?? null) == $lead->id)
            ->each->delete();
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
}