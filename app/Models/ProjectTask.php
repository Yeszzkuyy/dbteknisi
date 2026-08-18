<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← Tambahkan

class ProjectTask extends Model
{
    use SoftDeletes; // ← Tambahkan

    protected $fillable = [
        'project_id',
        'task_name',
        'assigned_to',
        'due_date',
        'status',
        'priority',
        'description',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}