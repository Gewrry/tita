<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class StockMovement extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'stock_in' => 'Stock In',
            'sale' => 'Sale',
            'adjustment' => 'Adjustment',
            'spoilage' => 'Spoilage',
            'return' => 'Return',
            default => ucfirst($this->type),
        };
    }
}
