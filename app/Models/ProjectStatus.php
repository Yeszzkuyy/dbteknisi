<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStatus extends Model
{
    use SoftDeletes;

    /**
     * Palet warna badge per nama status, satu sumber untuk dashboard teknisi
     * dan daftar project. Nilai = kunci warna komponen <x-status-badge>.
     */
    public const BADGE_COLORS = [
        'Open' => 'blue',
        'On Progress' => 'yellow',
        'Pending' => 'orange',
        'Hold' => 'red',
        'Done' => 'green',
        'Cancelled' => 'slate',
        'Warranty' => 'cyan',
        'Maintenance' => 'purple',
    ];

    protected $fillable = [
        'name',
        'color',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relasi ke Project
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Scope untuk default status
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}