<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'status',
        'source',
        'notes',
        'opportunity_value',
        'expected_close_date',
        'assigned_to',
    ];

    protected $casts = [
        'opportunity_value' => 'decimal:2',
        'expected_close_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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