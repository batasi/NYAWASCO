<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMeterBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'meter_id',
        'balance_bf',
        'current_balance',
        'total_paid',
        'total_billed',
        'installation_date',
        'initial_reading',
        'final_reading',
        'assigned_at',
        'unassigned_at',
        'unassignment_reason',
        'status',
        'notes'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'assigned_at' => 'date',
        'unassigned_at' => 'date',
        'balance_bf' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_billed' => 'decimal:2',
        'initial_reading' => 'decimal:2',
        'final_reading' => 'decimal:2'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function closeBalance($finalReading, $reason = null)
    {
        $this->update([
            'status' => 'closed',
            'unassigned_at' => now(),
            'final_reading' => $finalReading,
            'unassignment_reason' => $reason
        ]);
    }
}
