<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class RestaurantTable extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'table_number',
        'name',
        'capacity',
        'status',
        'sort_order',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class, 'table_id')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: "Table {$this->table_number}";
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'emerald',
            'occupied' => 'red',
            'reserved' => 'amber',
            'dirty' => 'gray',
            default => 'gray',
        };
    }
}
