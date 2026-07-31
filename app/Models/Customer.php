<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToUser;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'notes',
        'credit_limit',
        'is_credit_allowed',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_credit_allowed' => 'boolean',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function createUnknown(): self
    {
        $nextNumber = static::withTrashed()
            ->where('name', 'like', 'Unknown Customer %')
            ->when(Auth::check(), fn ($query) => $query->where('user_id', Auth::id()))
            ->count() + 1;

        while (static::withTrashed()
            ->when(Auth::check(), fn ($query) => $query->where('user_id', Auth::id()))
            ->where('name', "Unknown Customer {$nextNumber}")
            ->exists()) {
            $nextNumber++;
        }

        return static::create([
            'name' => "Unknown Customer {$nextNumber}",
            'notes' => 'Automatically created for an invoice where the customer name was not known yet.',
        ]);
    }

    public function getTotalBilledAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_billed - $this->total_paid;
    }

    /**
     * Check if customer can take on more credit.
     */
    public function canCredit(float $amount = 0): bool
    {
        if (!$this->is_credit_allowed) return false;
        if (is_null($this->credit_limit)) return true; // unlimited
        return ($this->balance + $amount) <= (float) $this->credit_limit;
    }

    public function getRemainingCreditAttribute(): ?float
    {
        if (is_null($this->credit_limit)) return null;
        return max(0, (float) $this->credit_limit - $this->balance);
    }
}
