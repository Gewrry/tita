<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToUser;

class Category extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }
}
