<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const PT_GROUPS = ['NTI', 'MGK', 'TPS', 'WANI'];

    protected $fillable = [
        'customer_id',
        'pt_group',
        'segment',
        'status',
        'source',
        'kebutuhan',
        'notes',
        'incoming_date',
        'assigned_to',
    ];

    protected $casts = [
        'incoming_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

    public function projects()
    {
        return $this->customer->projects();
    }
}