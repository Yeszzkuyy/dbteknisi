<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'sales_request', 'survey_date', 'location', 'pic',
        'survey_data', 'documentation', 'survey_report', 'status', 'notes',
    ];

    protected $casts = [
        'survey_date' => 'date',
        'documentation' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
