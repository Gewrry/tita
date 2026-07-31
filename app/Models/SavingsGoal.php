<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsGoal extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'start_date',
        'goal_date',
        'color_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'date',
        'goal_date' => 'date',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    public function currentBalance(): float
    {
        $deposits = $this->transactions()->where('type', 'deposit')->sum('amount');
        $withdrawals = $this->transactions()->where('type', 'withdrawal')->sum('amount');

        return (float) ($deposits - $withdrawals);
    }

    public function progressPercentage(): float
    {
        if ($this->target_amount <= 0) return 0;
        
        $percentage = ($this->currentBalance() / $this->target_amount) * 100;
        
        return min(100, max(0, $percentage));
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->goal_date) return 0;
        
        $days = now()->diffInDays($this->goal_date, false);
        
        return $days > 0 ? (int) $days : 0;
    }
}
