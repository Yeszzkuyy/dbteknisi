<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public const TYPES = [
        'supplier' => 'Supplier',
        'vendor' => 'Vendor',
        'kontraktor' => 'Kontraktor',
        'partner' => 'Partner',
        'distributor' => 'Distributor',
    ];

    public const TYPE_DOTS = [
        'supplier' => '#3b82f6',
        'vendor' => '#a855f7',
        'kontraktor' => '#f97316',
        'partner' => '#22c55e',
        'distributor' => '#eab308',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}