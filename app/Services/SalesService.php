<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\FollowUp;
use App\Models\Customer;
use App\Models\LeadActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function getMeetings(array $filters = [])
    {
        $query = Meeting::with(['customer', 'creator', 'followUps', 'lead']);

        if (!empty($filters['search'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->whereLike('name', $filters['search']);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('meeting_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('meeting_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['lead_id'])) {
            $query->where('lead_id', $filters['lead_id']);
        }

        $this->scopeToOwnLeads($query);

        return $query->latest('meeting_date')->paginate(15);
    }

    public function createMeeting(array $data): Meeting
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $meeting = Meeting::create($data);
            if (!empty($meeting->lead_id)) {
                LeadActivity::create([
                    'lead_id' => $meeting->lead_id,
                    'user_id' => auth()->id(),
                    'action' => 'meeting_created',
                ]);
            }

            return $meeting;
        });
    }

    public function updateMeeting(Meeting $meeting, array $data): Meeting
    {
        $meeting->update($data);
        return $meeting;
    }

    public function deleteMeeting(Meeting $meeting): void
    {
        $meeting->delete();
    }

    public function getFollowUps(array $filters = [])
    {
        $query = FollowUp::with(['customer', 'meeting', 'creator', 'lead']);

        if (!empty($filters['search'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->whereLike('name', $filters['search']);
            });
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['lead_id'])) {
            $query->where('lead_id', $filters['lead_id']);
        }

        // overdue: 1 = jatuh tempo (< hari ini, kompatibel link lama),
        // today = hari ini, upcoming = setelah hari ini.
        // Selesai tidak ikut hitungan jatuh tempo / hari ini / mendatang.
        if (!empty($filters['overdue'])) {
            $query->whereNull('completed_at')
                ->whereNotNull('follow_up_date');
            if ($filters['overdue'] === 'today') {
                $query->whereDate('follow_up_date', today());
            } elseif ($filters['overdue'] === 'upcoming') {
                $query->whereDate('follow_up_date', '>', today());
            } else {
                $query->whereDate('follow_up_date', '<', today());
            }
        }

        $this->scopeToOwnLeads($query);

        return $query->latest('follow_up_date')->paginate(15);
    }

    public function createFollowUp(array $data): FollowUp
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $followUp = FollowUp::create($data);
            if (!empty($followUp->lead_id)) {
                LeadActivity::create([
                    'lead_id' => $followUp->lead_id,
                    'user_id' => auth()->id(),
                    'action' => 'followup_created',
                ]);
            }

            return $followUp;
        });
    }

    public function updateFollowUp(FollowUp $followUp, array $data): FollowUp
    {
        $followUp->update($data);
        return $followUp;
    }

    public function deleteFollowUp(FollowUp $followUp): void
    {
        $followUp->delete();
    }

    public function completeFollowUp(FollowUp $followUp): FollowUp
    {
        $followUp->forceFill(['completed_at' => now()])->save();
        $this->logFollowUpActivity($followUp, 'followup_completed');

        return $followUp;
    }

    public function reopenFollowUp(FollowUp $followUp): FollowUp
    {
        $followUp->forceFill(['completed_at' => null])->save();

        return $followUp;
    }

    public function snoozeFollowUp(FollowUp $followUp, int $days): FollowUp
    {
        $base = $followUp->follow_up_date && $followUp->follow_up_date->isFuture()
            ? $followUp->follow_up_date
            : today();

        $followUp->forceFill([
            'follow_up_date' => $base->copy()->addDays($days),
            'reminder_sent_at' => null, // Ingatkan lagi setelah ditunda.
        ])->save();
        $this->logFollowUpActivity($followUp, 'followup_snoozed', ['days' => $days]);

        return $followUp;
    }

    public function rescheduleFollowUp(FollowUp $followUp, Carbon $date): FollowUp
    {
        $followUp->forceFill([
            'follow_up_date' => $date->copy()->startOfDay(),
            'reminder_sent_at' => null, // Ingatkan lagi setelah dijadwal ulang.
        ])->save();
        $this->logFollowUpActivity($followUp, 'followup_snoozed', ['date' => $date->toDateString()]);

        return $followUp;
    }

    private function logFollowUpActivity(FollowUp $followUp, string $action, ?array $changes = null): void
    {
        if (empty($followUp->lead_id)) {
            return;
        }

        LeadActivity::create([
            'lead_id' => $followUp->lead_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }

    public function getCustomerMeetings(Customer $customer)
    {
        return $customer->meetings()->with(['creator', 'followUps'])->latest('meeting_date')->get();
    }

    public function getCustomerFollowUps(Customer $customer)
    {
        return $customer->followUps()->with(['meeting', 'creator'])->latest('follow_up_date')->get();
    }

    /**
     * Sales biasa hanya melihat meeting/follow-up dari lead miliknya
     * (atau yang ia buat sendiri untuk data lama tanpa lead_id).
     */
    private function scopeToOwnLeads($query): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('sales')) {
            return;
        }
        if ($user->can('manage-marketing') || $user->can('manage-sales-leads')) {
            return;
        }

        $userId = $user->id;
        $query->where(function ($q) use ($userId) {
            $q->whereHas('lead', fn ($l) => $l->where('assigned_to', $userId))
                ->orWhere(fn ($w) => $w->whereNull('lead_id')->where('created_by', $userId));
        });
    }
}
