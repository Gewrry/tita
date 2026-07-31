<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToUser;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'total_amount',
        'penalty_amount',
        'penalty_type',
        'penalty_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'penalty_value' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return ($this->total_amount + $this->penalty_amount) - $this->total_paid;
    }

    public function recalculateTotal()
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }

    public function updateStatus()
    {
        $balance = $this->balance;
        $totalWithPenalty = $this->total_amount + $this->penalty_amount;

        if ($balance <= 0) {
            $this->status = 'paid';
        } elseif ($balance < $totalWithPenalty) {
            $this->status = 'partial';
        } elseif ($this->due_date->isPast()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }

    public function applyPenalty()
    {
        if ($this->penalty_type === 'flat') {
            $this->penalty_amount = $this->penalty_value;
        } elseif ($this->penalty_type === 'percentage') {
            $this->penalty_amount = ($this->penalty_value / 100) * $this->balance;
        }
        $this->save();
    }

    public static function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('Ym') . '-';
        $latest = static::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($latest) {
            $number = (int) substr($latest->invoice_number, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
