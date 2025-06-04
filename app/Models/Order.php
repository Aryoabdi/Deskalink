<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'user_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'order_id');
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

    // Helper methods
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getFormattedTotal()
    {
        return number_format($this->total_amount, 2, ',', '.');
    }

    // Generate unique order ID
    public static function generateOrderId()
    {
        $prefix = 'ORD';
        $timestamp = time();
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        return $prefix . $timestamp . $random;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_id)) {
                $order->order_id = self::generateOrderId();
            }
        });
    }
}
