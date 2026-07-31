<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsTransaction extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'savings_goal_id',
        'type',
        'amount',
        'transaction_date',
        'purpose',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public static function currentBalance(): float
    {
        $deposits = static::where('type', 'deposit')->sum('amount');
        $withdrawals = static::where('type', 'withdrawal')->sum('amount');

        return (float) ($deposits - $withdrawals);
    }

    public function goal()
    {
        return $this->belongsTo(SavingsGoal::class, 'savings_goal_id');
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->type === 'deposit'
            ? (float) $this->amount
            : (float) $this->amount * -1;
    }
}
