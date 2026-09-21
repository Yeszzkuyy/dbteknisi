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
        'whatsapp',
        'email',
        'notes',
        'status',
        'deleted_by',
        'whatsapp_account_id',
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

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    public function waLink(string $text = ''): ?string
    {
        $number = $this->whatsapp ?: $this->phone;
        $digits = preg_replace('/\D/', '', $number ?? '');

        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return 'https://wa.me/' . $digits . ($text ? '?text=' . rawurlencode($text) : '');
    }
}