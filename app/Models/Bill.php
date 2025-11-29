<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'meter_id',
        'meter_reading_id',
        'bill_number',
        'billing_period_start',
        'billing_period_end',
        'consumption',
        'base_charge',
        'consumption_charge',
        'tax_amount',
        'late_fee',
        'total_amount',
        'due_date',
        'bill_status',
        'payment_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'base_charge' => 'decimal:2',
        'consumption_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'consumption' => 'decimal:2',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function meterReading()
    {
        return $this->belongsTo(MeterReading::class, 'meter_reading_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('bill_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('bill_status', 'paid');
    }

    public function scopePartial($query)
    {
        return $query->where('bill_status', 'partial');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->unpaid();
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return 'KSh ' . number_format($this->total_amount, 2);
    }

    public function getFormattedDueDateAttribute()
    {
        return $this->due_date?->format('M d, Y');
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->lt(now()) && $this->bill_status === 'unpaid';
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getDueAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getPaymentPercentageAttribute()
    {
        return $this->total_amount > 0 ? ($this->paid_amount / $this->total_amount) * 100 : 0;
    }
}