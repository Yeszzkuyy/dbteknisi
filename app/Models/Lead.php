<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const PT_GROUPS = ['NTI', 'MGK', 'TPS', 'WANI'];

    public const LOST_REASONS = ['price', 'competitor', 'budget', 'requirement_changed', 'no_response', 'other'];

    public static function lostReasonLabel(?string $reason): string
    {
        return match ($reason) {
            'price' => __('Harga'),
            'competitor' => __('Kompetitor'),
            'budget' => __('Budget'),
            'requirement_changed' => __('Kebutuhan berubah'),
            'no_response' => __('Tidak ada respon'),
            'other' => __('Lainnya'),
            default => '-',
        };
    }

    public const PT_COLORS = [
        'NTI' => 'bg-sky-500 text-white',
        'MGK' => 'bg-blue-900 text-white',
        'TPS' => 'bg-red-500 text-white',
        'WANI' => 'bg-orange-500 text-white',
    ];

    protected $fillable = [
        'customer_id',
        'partner_id',
        'whatsapp_account_id',
        'pt_group',
        'segment',
        'status',
        'source',
        'kebutuhan',
        'solusi',
        'progress_notes',
        'notes',
        'incoming_date',
        'assigned_to',
        'lost_reason',
        'lost_note',
        'closed_at',
        'closing_note',
    ];

    protected $casts = [
        'incoming_date' => 'date',
        'assigned_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer()
    {
        // Lead tetap harus menampilkan customernya walau customer sudah soft-deleted
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function projects()
    {
        return $this->customer->projects();
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function tasks()
    {
        return $this->hasMany(LeadTask::class);
    }

    public function salesSchedules()
    {
        return $this->hasMany(SalesSchedule::class);
    }

    public function technicalRequests()
    {
        return $this->hasMany(TechnicalRequest::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}