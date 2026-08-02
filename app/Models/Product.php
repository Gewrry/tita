<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToUser;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'reorder_level',
        'unit',
        'is_active',
        'track_stock',
        'image_path',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'track_stock' => 'boolean',
    ];

    public static $units = [
        'piece' => 'Piece',
        'pack' => 'Pack',
        'case' => 'Case',
        'kg' => 'Kilogram',
        'g' => 'Gram',
        'liter' => 'Liter',
        'ml' => 'Milliliter',
        'serving' => 'Serving',
        'order' => 'Order',
        'bottle' => 'Bottle',
        'can' => 'Can',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function unitConversions()
    {
        return $this->hasMany(UnitConversion::class);
    }

    public function pricingProfile()
    {
        return $this->hasOne(ProductPricingProfile::class);
    }

    public function pricingIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function pricingHistories()
    {
        return $this->hasMany(PricingHistory::class)->latest('effective_date')->latest();
    }

    public function foodSales()
    {
        return $this->hasMany(FoodSale::class);
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock_quantity <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    /**
     * Adjust stock and record the movement.
     */
    public function adjustStock(int $quantity, string $type, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null): StockMovement
    {
        $stockBefore = $this->stock_quantity;
        $this->stock_quantity += $quantity;
        $this->save();

        return StockMovement::create([
            'product_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);
    }

    public function getProfit(): float
    {
        return (float) $this->selling_price - (float) $this->cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->selling_price === 0.0) return 0;
        return ($this->getProfit() / (float) $this->selling_price) * 100;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }
}
