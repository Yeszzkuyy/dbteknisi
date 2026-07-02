<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'activity_date',
        'type',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'activity_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}