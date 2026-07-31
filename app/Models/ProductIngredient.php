<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'quantity_per_serving',
        'unit',
        'cost_per_unit',
        'is_estimated',
        'notes',
    ];

    protected $casts = [
        'quantity_per_serving' => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
        'is_estimated' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCostPerServingAttribute(): float
    {
        return round((float) $this->quantity_per_serving * (float) $this->cost_per_unit, 2);
    }
}
