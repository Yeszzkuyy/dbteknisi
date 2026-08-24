<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Pastikan ini ada

class Customer extends Model
{
    use SoftDeletes; // Pastikan ini ada

    protected $fillable = [
        'name',
        'contact_person',
        'company',
        'address',
        'phone',
        'email',
        'notes',
        'status',
        'deleted_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}