<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSupport extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'project_id',
        'user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function engineer()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}