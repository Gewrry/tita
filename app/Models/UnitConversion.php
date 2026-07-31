<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    protected $fillable = [
        'product_id',
        'bulk_unit',
        'bulk_quantity',
        'retail_unit',
        'retail_quantity',
        'retail_price',
    ];

    protected $casts = [
        'retail_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the label for this conversion, e.g. "1 Case = 24 Pieces"
     */
    public function getLabel(): string
    {
        return "{$this->bulk_quantity} {$this->bulk_unit} = {$this->retail_quantity} {$this->retail_unit}";
    }
}
