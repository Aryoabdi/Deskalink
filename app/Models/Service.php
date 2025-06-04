<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $primaryKey = 'service_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'service_id',
        'partner_id',
        'title',
        'description',
        'price',
        'status',
        'category',
        'thumbnail'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    public function moderationLogs()
    {
        return $this->hasMany(ModerationLog::class, 'content_id', 'service_id')
                    ->where('content_type', 'service');
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'service_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isBanned()
    {
        return $this->status === 'banned';
    }

    public function getFormattedPrice()
    {
        return number_format($this->price, 2, ',', '.');
    }
}
