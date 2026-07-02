<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Pastikan ini ada

class Customer extends Model
{
    use SoftDeletes; // Pastikan ini ada

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