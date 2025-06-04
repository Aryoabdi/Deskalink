<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'partner_id',
        'name',
        'description',
        'price',
        'image_url',
        'status',
        'category'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'deleted' => 'Deleted'
    ];

    const CATEGORIES = [
        'product' => 'Product',
        'service' => 'Service'
    ];

    // Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getCategoryLabel()
    {
        return self::CATEGORIES[$this->category] ?? 'Unknown';
    }

    public function getFormattedPrice()
    {
        return number_format($this->price, 2, ',', '.');
    }

    // Generate unique product ID
    public static function generateProductId()
    {
        $prefix = 'PRD';
        $timestamp = time();
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        return $prefix . $timestamp . $random;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->product_id)) {
                $product->product_id = self::generateProductId();
            }
        });

        static::deleting(function ($product) {
            // Here you could add logic to delete the product image if needed
            // For example: Storage::delete($product->image_url);
        });
    }
}
