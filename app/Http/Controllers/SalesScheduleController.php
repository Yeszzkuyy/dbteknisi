<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\SalesSchedule;
use App\Models\User;
use App\Notifications\SalesScheduleAssignedNotification;
use Illuminate\Http\Request;

class SalesScheduleController extends Controller
{
    private function ownScope($query)
    {
        $user = auth()->user();

        if ($user->can('manage-sales-leads')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('sales_schedules.assigned_to', $user->id)
                ->orWhere('sales_schedules.created_by', $user->id)
                ->orWhereHas('lead', fn ($l) => $l->where('assigned_to', $user->id));
        });
    }

    private function authorizeSchedule(SalesSchedule $schedule): void
    {
        $this->authorize('view', $schedule->lead);

        $user = auth()->user();
        $involved = in_array($user->id, [$schedule->assigned_to, $schedule->created_by, $schedule->lead->assigned_to]);

        abort_unless($involved || $user->can('manage-sales-leads'), 403);
    }

    private function authorizeManage(SalesSchedule $schedule): void
    {
        $this->authorizeSchedule($schedule);

        $user = auth()->user();
        abort_unless(
            in_array($user->id, [$schedule->assigned_to, $schedule->created_by])
                || $user->can('manage-sales-leads'),
            403
        );
    }

    private function log(Lead $lead, string $action, array $changes = []): void
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');

        $query = $this->ownScope(
            SalesSchedule::with(['lead.customer', 'assignee'])->orderBy('start_at')
        );

        if ($tab === 'past') {
            $query->where(function ($q) {
                $q->where('start_at', '<', now()->startOfDay())
                    ->orWhereIn('status', ['completed', 'cancelled']);
            })->orderByDesc('start_at');
        } else {
            $query->where('start_at', '>=', now()->startOfDay())
                ->whereNotIn('status', ['completed', 'cancelled']);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        $schedules = $query->paginate(15)->withQueryString();

        return view('sales.schedules.index', compact('schedules', 'tab'));
    }

    public function create(Request $request)
    {
        $lead = Lead::findOrFail($request->get('lead_id'));
        $this->authorize('view', $lead);

        $pics = User::role(['sales', 'inside-sales'])->orderBy('name')->get(['id', 'name']);

        return view('sales.schedules.create', compact('lead', 'pics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', SalesSchedule::TYPES),
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder_at' => 'nullable|date|before:start_at',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        $this->authorize('view', $lead);

        $pic = User::findOrFail($validated['assigned_to']);
        abort_unless($pic->hasAnyRole(['sales', 'inside-sales']), 422, __('PIC harus Sales atau Inside Sales.'));

        $schedule = SalesSchedule::create($validated + ['created_by' => auth()->id()]);

        $this->log($lead, 'schedule_created', ['title' => $schedule->title]);

        if ((int) $schedule->assigned_to !== (int) auth()->id()) {
            $schedule->assignee?->notify(new SalesScheduleAssignedNotification($schedule));
        }

        return redirect()->route('sales.schedules.show', $schedule)
            ->with('success', __('Jadwal berhasil dibuat.'));
    }

    public function show(SalesSchedule $schedule)
    {
        $schedule->load(['lead.customer', 'assignee', 'creator']);
        $this->authorizeSchedule($schedule);

        return view('sales.schedules.show', compact('schedule'));
    }

    public function edit(SalesSchedule $schedule)
    {
        $this->authorizeManage($schedule);
        abort_if(in_array($schedule->status, ['completed', 'cancelled']), 422);

        $pics = User::role(['sales', 'inside-sales'])->orderBy('name')->get(['id', 'name']);

        return view('sales.schedules.edit', compact('schedule', 'pics'));
    }

    public function update(Request $request, SalesSchedule $schedule)
    {
        $this->authorizeManage($schedule);
        abort_if(in_array($schedule->status, ['completed', 'cancelled']), 422);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', SalesSchedule::TYPES),
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder_at' => 'nullable|date|before:start_at',
        ]);

        $rescheduled = $schedule->status === 'scheduled'
            && $validated['start_at'] !== $schedule->start_at->format('Y-m-d H:i:s');

        $schedule->update($validated + ($rescheduled ? ['status' => 'rescheduled'] : []));

        $this->log($schedule->lead, $rescheduled ? 'schedule_rescheduled' : 'schedule_updated', ['title' => $schedule->title]);

        if ((int) $schedule->assigned_to !== (int) auth()->id()) {
            $schedule->assignee?->notify(new SalesScheduleAssignedNotification($schedule));
        }

        return redirect()->route('sales.schedules.show', $schedule)
            ->with('success', __('Jadwal berhasil diperbarui.'));
    }

    public function complete(SalesSchedule $schedule)
    {
        $this->authorizeManage($schedule);
        abort_if(in_array($schedule->status, ['completed', 'cancelled']), 422);

        $schedule->update(['status' => 'completed']);
        $this->log($schedule->lead, 'schedule_completed', ['title' => $schedule->title]);

        return back()->with('success', __('Jadwal ditandai selesai.'));
    }

    public function cancel(SalesSchedule $schedule)
    {
        $this->authorizeManage($schedule);
        abort_if(in_array($schedule->status, ['completed', 'cancelled']), 422);

        $schedule->update(['status' => 'cancelled']);
        $this->log($schedule->lead, 'schedule_cancelled', ['title' => $schedule->title]);

        return back()->with('success', __('Jadwal dibatalkan.'));
    }
}
