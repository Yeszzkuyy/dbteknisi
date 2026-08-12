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
}