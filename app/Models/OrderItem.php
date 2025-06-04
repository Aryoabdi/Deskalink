<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Helper methods
    public function getSubtotal()
    {
        return $this->quantity * $this->price;
    }

    public function getFormattedPrice()
    {
        return number_format($this->price, 2, ',', '.');
    }

    public function getFormattedSubtotal()
    {
        return number_format($this->getSubtotal(), 2, ',', '.');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            // If price is not set, get it from the product
            if (empty($orderItem->price)) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $orderItem->price = $product->price;
                }
            }
        });

        static::created(function ($orderItem) {
            // Update order total
            $order = $orderItem->order;
            if ($order) {
                $total = $order->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                $order->update(['total_amount' => $total]);
            }
        });

        static::deleted(function ($orderItem) {
            // Update order total when item is removed
            $order = $orderItem->order;
            if ($order) {
                $total = $order->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                $order->update(['total_amount' => $total]);
            }
        });
    }
}
