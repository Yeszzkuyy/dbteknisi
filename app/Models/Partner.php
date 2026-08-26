<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
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
}