<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Meeting;
use App\Services\SalesService;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function index(Request $request)
    {
        $followUps = $this->salesService->getFollowUps($request->only(['search', 'customer_id']));
        $customers = Customer::orderBy('name')->get(['id', 'name']);

        return view('sales.follow-ups.index', compact('followUps', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $meetings = collect();

        $customerId = $request->get('customer_id');
        $meetingId = $request->get('meeting_id');

        if ($customerId) {
            $meetings = Meeting::where('customer_id', $customerId)->orderBy('meeting_date', 'desc')->get(['id', 'meeting_date']);
        }

        return view('sales.follow-ups.create', compact('customers', 'meetings', 'customerId', 'meetingId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_id' => 'nullable|exists:meetings,id',
            'description' => 'required|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $this->salesService->createFollowUp($validated);

        return redirect()->route('sales.follow-ups.index')
            ->with('success', 'Follow up berhasil dicatat.');
    }

    public function show(FollowUp $followUp)
    {
        $followUp->load(['customer', 'meeting', 'creator']);
        return view('sales.follow-ups.show', compact('followUp'));
    }

    public function edit(FollowUp $followUp)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $meetings = Meeting::where('customer_id', $followUp->customer_id)
            ->orderBy('meeting_date', 'desc')->get(['id', 'meeting_date']);

        return view('sales.follow-ups.edit', compact('followUp', 'customers', 'meetings'));
    }

    public function update(Request $request, FollowUp $followUp)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_id' => 'nullable|exists:meetings,id',
            'description' => 'required|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $this->salesService->updateFollowUp($followUp, $validated);

        return redirect()->route('sales.follow-ups.index')
            ->with('success', 'Follow up berhasil diupdate.');
    }

    public function destroy(FollowUp $followUp)
    {
        $this->salesService->deleteFollowUp($followUp);

        return redirect()->route('sales.follow-ups.index')
            ->with('success', 'Follow up berhasil dihapus.');
    }
}
