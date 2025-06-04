<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'google_id',
        'username',
        'password',
        'full_name',
        'email',
        'phone_number',
        'role',
        'status',
        'profile_image',
        'description',
        'is_profile_completed',
        'bio'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_profile_completed' => 'boolean',
    ];

    /**
     * Generate user_id in format "user00000001"
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $lastUser = User::orderBy('user_id', 'desc')->first();
            $lastId = $lastUser ? intval(substr($lastUser->user_id, 4)) : 0;
            $user->user_id = 'user' . str_pad($lastId + 1, 8, '0', STR_PAD_LEFT);
        });
    }

    // Relationships
    public function designs()
    {
        return $this->hasMany(Design::class, 'partner_id', 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'partner_id', 'user_id');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'partner_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'partner_id', 'user_id');
    }

    public function moderationLogs()
    {
        return $this->hasMany(ModerationLog::class, 'moderator_id', 'user_id');
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reported_by', 'user_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user', 'user_id');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPartner()
    {
        return $this->role === 'partner';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
