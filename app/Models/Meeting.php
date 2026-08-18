<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'meeting_date',
        'participants',
        'user_needs',
        'user_complaints',
        'existing_system',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }
}
