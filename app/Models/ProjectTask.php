<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'start_date',
        'due_date',
        'completed_at',
    ];

    public function project()
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function engineer()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }
}
