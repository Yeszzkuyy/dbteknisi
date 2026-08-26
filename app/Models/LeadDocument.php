<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadDocument extends Model
{
    protected $fillable = [
        'lead_id',
        'file_name',
        'file_path',
        'mime_type',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
