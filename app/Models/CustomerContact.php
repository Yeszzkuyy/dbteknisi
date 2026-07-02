<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'customer_id',
        'name',
        'position',
        'phone',
        'email',
        'is_primary',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}