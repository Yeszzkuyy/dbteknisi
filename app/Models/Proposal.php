<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'draft', 'ready', 'sent', 'viewed', 'revision', 'accepted', 'rejected', 'expired', 'cancelled',
    ];

    protected $fillable = [
        'lead_id',
        'proposal_number',
        'created_by',
        'proposal_date',
        'valid_until',
        'customer',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'notes',
        'status',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function recalculate(): void
    {
        $subtotal = (float) $this->items()->sum('subtotal');
        $discount = min((float) ($this->discount ?? 0), $subtotal);
        $tax = max(0, (float) ($this->tax ?? 0));
        $this->forceFill([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'grand_total' => $subtotal - $discount + $tax,
        ])->save();
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'draft' => __('Draft'),
            'ready' => __('Ready'),
            'sent' => __('Sent'),
            'viewed' => __('Viewed'),
            'revision' => __('Revision Requested'),
            'accepted' => __('Accepted'),
            'rejected' => __('Rejected'),
            'expired' => __('Expired'),
            'cancelled' => __('Cancelled'),
            default => $status,
        };
    }
}
