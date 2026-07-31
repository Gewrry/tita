<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'amount',
        'special_instructions',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            $item->order->recalculateTotal();
        });

        static::deleted(function ($item) {
            $item->order->recalculateTotal();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
