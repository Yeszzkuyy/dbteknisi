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

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $preselectedCustomerId = $request->query('customer_id');
        return view('sales.meetings.create', compact('customers', 'preselectedCustomerId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $validated['customer_id'] = Customer::create(['name' => $validated['customer_name']])->id;
        }

        unset($validated['customer_mode'], $validated['customer_name']);

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
            'customer_mode' => 'required|in:new,existing',
            'customer_name' => 'nullable|required_if:customer_mode,new|string|max:255',
            'customer_id' => 'nullable|required_if:customer_mode,existing|exists:customers,id',
            'meeting_date' => 'required|date',
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $validated['customer_id'] = Customer::create(['name' => $validated['customer_name']])->id;
        }

        unset($validated['customer_mode'], $validated['customer_name']);

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
