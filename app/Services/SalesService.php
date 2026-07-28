<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\FollowUp;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function getMeetings(array $filters = [])
    {
        $query = Meeting::with(['customer', 'creator', 'followUps']);

        if (!empty($filters['search'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
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

        return $query->latest('meeting_date')->paginate(15);
    }

    public function createMeeting(array $data): Meeting
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            return Meeting::create($data);
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
        $query = FollowUp::with(['customer', 'meeting', 'creator']);

        if (!empty($filters['search'])) {
            $query->whereHas('customer', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest('follow_up_date')->paginate(15);
    }

    public function createFollowUp(array $data): FollowUp
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            return FollowUp::create($data);
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

    public function getCustomerMeetings(Customer $customer)
    {
        return $customer->meetings()->with(['creator', 'followUps'])->latest('meeting_date')->get();
    }

    public function getCustomerFollowUps(Customer $customer)
    {
        return $customer->followUps()->with(['meeting', 'creator'])->latest('follow_up_date')->get();
    }
}
