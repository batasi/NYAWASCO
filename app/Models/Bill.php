<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'customer_id',
        'meter_reading_id',
        'consumption',
        'rate_per_unit',
        'amount',
        'fixed_charge',
        'tax_amount',
        'total_amount',
        'billing_date',
        'due_date',
        'status',
        'billing_notes',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'consumption' => 'decimal:2',
        'rate_per_unit' => 'decimal:2',
        'amount' => 'decimal:2',
        'fixed_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $bill->bill_number = 'BL' . date('Ymd') . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
            
            // Calculate amounts
            $bill->amount = $bill->consumption * $bill->rate_per_unit;
            $bill->total_amount = $bill->amount + $bill->fixed_charge + $bill->tax_amount;
            
            // Set due date (30 days from billing date)
            if (empty($bill->due_date)) {
                $bill->due_date = \Carbon\Carbon::parse($bill->billing_date)->addDays(30);
            }
        });

        static::created(function ($bill) {
            // Mark the meter reading as billed
            $bill->meterReading->update([
                'billed' => true,
                'billed_by' => auth()->id(),
                'billed_at' => now(),
            ]);
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meterReading()
    {
        return $this->belongsTo(MeterReading::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    // Helper methods
    public function getFormattedTotalAttribute()
    {
        return 'KSh ' . number_format($this->total_amount, 2);
    }

    public function isOverdue()
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getAmountPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    public function isFullyPaid()
    {
        return $this->balance <= 0;
    }
}