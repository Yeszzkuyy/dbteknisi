<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStatus extends Model
{
    use SoftDeletes;

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

    public function badgeClasses(): string
    {
        return match ($this->color) {
            'green'   => 'bg-green-100 text-green-800 border border-green-200',
            'yellow'  => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'orange'  => 'bg-orange-100 text-orange-800 border border-orange-200',
            'red'     => 'bg-red-100 text-red-800 border border-red-200',
            'purple'  => 'bg-purple-100 text-purple-800 border border-purple-200',
            'pink'    => 'bg-pink-100 text-pink-800 border border-pink-200',
            'gray'    => 'bg-slate-100 text-slate-700 border border-slate-200',
            default   => 'bg-blue-100 text-blue-800 border border-blue-200',
        };
    }
}