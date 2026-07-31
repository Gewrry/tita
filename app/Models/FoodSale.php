<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class FoodSale extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'sale_date',
        'quantity',
        'selling_price',
        'unit_cost',
        'total_revenue',
        'total_cost',
        'gross_profit',
        'channel',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'quantity' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'gross_profit' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
