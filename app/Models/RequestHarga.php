<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestHarga extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'items', 'notes', 'attachments', 'status',
    ];

    protected $casts = [
        'items' => 'array',
        'attachments' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
