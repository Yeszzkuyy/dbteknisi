<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingDraft extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DISCARDED = 'discarded';

    protected $fillable = [
        'created_by',
        'customer_id',
        'lead_id',
        'meeting_date',
        'participants',
        'user_needs',
        'user_complaints',
        'existing_system',
        'notes',
        'source_sentence',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
