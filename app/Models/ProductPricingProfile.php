<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class ProductPricingProfile extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'packaging_cost',
        'labor_allowance',
        'utility_allowance',
        'transportation_cost',
        'delivery_fees',
        'waste_percentage',
        'minimum_margin',
        'desired_margin',
        'complete_cost_per_serving',
        'previous_cost_per_serving',
        'smart_rounding',
        'last_recommendation',
        'last_recommended_at',
    ];

    protected $casts = [
        'packaging_cost' => 'decimal:2',
        'labor_allowance' => 'decimal:2',
        'utility_allowance' => 'decimal:2',
        'transportation_cost' => 'decimal:2',
        'delivery_fees' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'minimum_margin' => 'decimal:2',
        'desired_margin' => 'decimal:2',
        'complete_cost_per_serving' => 'decimal:2',
        'previous_cost_per_serving' => 'decimal:2',
        'smart_rounding' => 'boolean',
        'last_recommendation' => 'array',
        'last_recommended_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
