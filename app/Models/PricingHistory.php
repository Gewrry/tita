<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class PricingHistory extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'previous_price',
        'recommended_price',
        'approved_price',
        'previous_cost_per_serving',
        'updated_cost_per_serving',
        'effective_date',
        'reason',
        'approved_by',
        'action',
        'metadata',
    ];

    protected $casts = [
        'previous_price' => 'decimal:2',
        'recommended_price' => 'decimal:2',
        'approved_price' => 'decimal:2',
        'previous_cost_per_serving' => 'decimal:2',
        'updated_cost_per_serving' => 'decimal:2',
        'effective_date' => 'date',
        'metadata' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
