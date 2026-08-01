<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToUser;

class Expense extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'product_id',
        'description',
        'amount',
        'category',
        'expense_date',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public static $categories = [
        'supplies' => 'Supplies',
        'rent' => 'Rent',
        'salary' => 'Salary',
        'utilities' => 'Utilities',
        'transportation' => 'Transportation',
        'food' => 'Food',
        'other' => 'Other',
    ];
}
