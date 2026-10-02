<?php

namespace App\Http\Controllers;

use App\Ai\Agents\MeetingUpdateDrafter;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\MeetingDraft;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MeetingDraftController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'lead_id' => 'nullable|exists:leads,id',
            'source_sentence' => 'required|string|max:500',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $lead = ! empty($validated['lead_id']) ? Lead::find($validated['lead_id']) : null;

        try {
            $fields = $this->draftFields($validated['source_sentence'], $customer, $lead);
        } catch (Throwable $e) {
            Log::error('Meeting draft AI gagal: '.$e->getMessage());

            return back()->with('error', __('AI failed to compose the draft. Please try again.'));
        }

        MeetingDraft::create([
            'created_by' => auth()->id(),
            'customer_id' => $customer->id,
            'lead_id' => $lead?->id,
            'meeting_date' => today()->toDateString(),
            'source_sentence' => $validated['source_sentence'],
            ...$fields,
        ]);

        return redirect()
            ->route('sales.meetings.index')
            ->with('success', __('Draft ready. Please review and approve.'));
    }

    public function approve(Request $request, MeetingDraft $meetingDraft)
    {
        $this->authorizeDraft($meetingDraft);

        $validated = $request->validate([
            'participants' => 'nullable|string|max:500',
            'user_needs' => 'nullable|string',
            'user_complaints' => 'nullable|string',
            'existing_system' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->salesService->createMeeting([
            'customer_id' => $meetingDraft->customer_id,
            'lead_id' => $meetingDraft->lead_id,
            'meeting_date' => $meetingDraft->meeting_date?->toDateString() ?? today()->toDateString(),
            ...$validated,
        ]);

        $meetingDraft->update(['status' => MeetingDraft::STATUS_APPROVED]);

        return redirect()
            ->route('sales.meetings.index')
            ->with('success', __('Meeting logged.'));
    }

    public function discard(MeetingDraft $meetingDraft)
    {
        $this->authorizeDraft($meetingDraft);
        $meetingDraft->update(['status' => MeetingDraft::STATUS_DISCARDED]);

        return redirect()
            ->route('sales.meetings.index')
            ->with('success', __('Draft discarded.'));
    }

    private function authorizeDraft(MeetingDraft $meetingDraft): void
    {
        abort_if(
            (int) $meetingDraft->created_by !== (int) auth()->id()
                && ! auth()->user()->can('manage-sales-leads'),
            403
        );
        abort_if($meetingDraft->status !== MeetingDraft::STATUS_PENDING, 422, __('Draft is no longer pending.'));
    }

    /** Minta AI kembangkan kalimat sales + konteks sistem menjadi kolom meeting. */
    private function draftFields(string $sentence, Customer $customer, ?Lead $lead): array
    {
        $context = [
            'Sales note: '.$sentence,
            'Customer: '.$customer->name,
        ];

        if ($lead) {
            $context[] = 'Lead status: '.$lead->status
                .' | Needs: '.str($lead->kebutuhan)->limit(200)
                .' | Progress: '.str($lead->progress_notes)->limit(200);
        }

        FollowUp::with('creator:id,name')
            ->where('customer_id', $customer->id)
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->limit(5)
            ->get()
            ->each(fn ($fu) => $context[] = 'Today follow-up ('.$fu->creator?->name.'): '.str($fu->description)->limit(200));

        if ($last = Meeting::where('customer_id', $customer->id)->latest('meeting_date')->first()) {
            $context[] = 'Last meeting ('.$last->meeting_date->toDateString().'): '.str($last->user_needs)->limit(200);
        }

        $raw = trim((string) (new MeetingUpdateDrafter)->prompt(implode("\n", $context)));
        $json = json_decode(preg_replace('/^```(?:json)?|```$/m', '', $raw) ?? '', true);

        if (! is_array($json)) {
            throw new \RuntimeException('AI response is not valid JSON.');
        }

        $fields = [];
        foreach (['participants', 'user_needs', 'user_complaints', 'existing_system', 'notes'] as $key) {
            $fields[$key] = mb_substr(trim((string) ($json[$key] ?? '')), 0, 2000) ?: null;
        }

        return $fields;
    }
}
