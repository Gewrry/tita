<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'price',
        'amount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->amount = $item->quantity * $item->price;
        });

        static::saved(function ($item) {
            $item->invoice->recalculateTotal();
        });

        static::deleted(function ($item) {
            $item->invoice->recalculateTotal();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
