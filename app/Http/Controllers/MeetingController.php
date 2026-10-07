<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsAjax;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\MeetingDraft;
use App\Services\SalesService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    use RespondsAjax;

    public function __construct(private SalesService $salesService) {}

    public function index(Request $request)
    {
        $meetings = $this->salesService->getMeetings($request->only(['search', 'date_from', 'date_to', 'customer_id', 'lead_id']));
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $leads = $this->leadOptions(null);
        $drafts = MeetingDraft::pending()->ownedBy(auth()->id())
            ->with(['customer:id,name', 'lead:id,customer_id,status'])
            ->latest()
            ->limit(5)
            ->get();

        return $this->ajaxPartial($request, 'sales.meetings._table', compact('meetings', 'drafts', 'customers', 'leads'), 'sales.meetings.index');
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $preselectedCustomerId = $request->query('customer_id');
        $preselectedLeadId = $request->query('lead_id');
        $leads = $this->leadOptions($preselectedCustomerId);
        if ($preselectedLeadId && ($lead = Lead::with('customer')->find($preselectedLeadId))) {
            $preselectedCustomerId = $lead->customer_id;
        }
        $ptGroups = Lead::PT_GROUPS;
        return view('sales.meetings.create', compact('customers', 'preselectedCustomerId', 'leads', 'preselectedLeadId', 'ptGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'pt_group' => 'nullable|required_if:customer_mode,new|in:'.implode(',', Lead::PT_GROUPS),
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'required|string',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $customer = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_name'],
                'pt_group' => $validated['pt_group'],
            ]);
            $validated['customer_id'] = $customer->id;
            $validated['lead_id'] = null;
        } elseif (!empty($validated['lead_id'])) {
            $lead = Lead::find($validated['lead_id']);
            $validated['customer_id'] = $lead->customer_id;
        }

        unset($validated['customer_mode'], $validated['customer_name'], $validated['pt_group']);

        $this->salesService->createMeeting($validated);

        return $this->ajaxOrRedirect($request, 'sales.meetings.index',
            __('Meeting berhasil dicatat.'), ['redirect' => route('sales.meetings.index')]);
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['customer', 'creator', 'lead.customer', 'followUps' => fn($q) => $q->with('creator')]);
        return view('sales.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $leads = $this->leadOptions($meeting->customer_id);
        $ptGroups = Lead::PT_GROUPS;
        return view('sales.meetings.edit', compact('meeting', 'customers', 'leads', 'ptGroups'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'pt_group' => 'nullable|required_if:customer_mode,new|in:'.implode(',', Lead::PT_GROUPS),
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'required|string',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $customer = Customer::create([
                'name' => $validated['customer_name'],
                'company' => $validated['customer_name'],
                'pt_group' => $validated['pt_group'],
            ]);
            $validated['customer_id'] = $customer->id;
            $validated['lead_id'] = null;
        } elseif (!empty($validated['lead_id'])) {
            $lead = Lead::find($validated['lead_id']);
            $validated['customer_id'] = $lead->customer_id;
        }

        unset($validated['customer_mode'], $validated['customer_name'], $validated['pt_group']);

        $this->salesService->updateMeeting($meeting, $validated);

        return $this->ajaxOrRedirect($request, 'sales.meetings.index',
            __('Meeting berhasil diupdate.'), ['redirect' => route('sales.meetings.index')]);
    }

    public function destroy(Request $request, Meeting $meeting)
    {
        $this->salesService->deleteMeeting($meeting);

        return $this->ajaxOrRedirect($request, 'sales.meetings.index', __('Meeting berhasil dihapus.'));
    }

    private function leadOptions(?int $customerId = null)
    {
        $query = Lead::with('customer')->latest()->limit(100);
        if (auth()->user()?->hasRole('sales') && !auth()->user()?->can('manage-marketing')) {
            $query->where('assigned_to', auth()->id());
        }
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return $query->get(['id', 'customer_id', 'status', 'assigned_to']);
    }
}
