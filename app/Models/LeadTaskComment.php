<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTaskComment extends Model
{
    protected $fillable = [
        'lead_task_id',
        'user_id',
        'body',
    ];

    public function task()
    {
        return $this->belongsTo(LeadTask::class, 'lead_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
