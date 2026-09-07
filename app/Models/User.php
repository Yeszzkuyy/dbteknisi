<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // ← Tambahkan ini
use Laravel\Ai\Concerns\HasConversations;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasConversations, HasFactory, HasRoles, Notifiable, SoftDeletes; // ← Tambahkan SoftDeletes

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
