<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'notes',
    ];

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}