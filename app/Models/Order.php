<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'table_id',
        'order_number',
        'order_type',
        'status',
        'customer_id',
        'invoice_id',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recalculateTotal()
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $latest = static::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($latest) {
            $number = (int) substr($latest->order_number, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'served' => 'Served',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'preparing' => 'blue',
            'ready' => 'emerald',
            'served' => 'mint',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
