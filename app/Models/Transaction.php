<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_id',
        'user_id',
        'partner_id',
        'amount',
        'platform_fee',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    // Helper methods
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getFormattedAmount()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedPlatformFee()
    {
        return number_format($this->platform_fee, 2, ',', '.');
    }

    public function getNetAmount()
    {
        return $this->amount - $this->platform_fee;
    }

    public function getFormattedNetAmount()
    {
        return number_format($this->getNetAmount(), 2, ',', '.');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    // Generate unique transaction ID
    public static function generateTransactionId()
    {
        $prefix = 'TRX';
        $timestamp = time();
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        return $prefix . $timestamp . $random;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = self::generateTransactionId();
            }
        });
    }
}
