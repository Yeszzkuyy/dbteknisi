<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{

    protected $fillable = [
        'project_id',
        'document_category_id',
        'file_name',
        'file_path',
        'notes',
        'uploaded_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            DocumentCategory::class,
            'document_category_id'
        );
    }
}