<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Meeting;
use App\Services\SalesService;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function index(Request $request)
    {
        $meetings = $this->salesService->getMeetings($request->only(['search', 'date_from', 'date_to', 'customer_id']));
        $customers = Customer::orderBy('name')->get(['id', 'name']);

        return view('sales.meetings.index', compact('meetings', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        return view('sales.meetings.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->salesService->createMeeting($validated);

        return redirect()->route('sales.meetings.index')
            ->with('success', 'Meeting berhasil dicatat.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['customer', 'creator', 'followUps' => fn($q) => $q->with('creator')]);
        return view('sales.meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        return view('sales.meetings.edit', compact('meeting', 'customers'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->salesService->updateMeeting($meeting, $validated);

        return redirect()->route('sales.meetings.index')
            ->with('success', 'Meeting berhasil diupdate.');
    }

    public function destroy(Meeting $meeting)
    {
        $this->salesService->deleteMeeting($meeting);

        return redirect()->route('sales.meetings.index')
            ->with('success', 'Meeting berhasil dihapus.');
    }
}
