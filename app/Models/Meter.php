<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meter_number',
        'meter_type',
        'meter_model',
        'meter_category_id',
        'status',
        'customer_id',
        'installation_address',
        'installation_date',
        'last_maintenance_date',
        'initial_reading',
        'installation_fee',
        'connection_fee',
        'deposit_amount',
        'balance_bf',
        'current_balance',
        'additional_charges',
        'notes',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'last_maintenance_date' => 'date',
        'initial_reading' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'connection_fee' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_bf' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'additional_charges' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meter) {
            if (empty($meter->meter_number)) {
                $meter->meter_number = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meterCategory()
    {
        return $this->belongsTo(MeterCategory::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function latestReading()
    {
        return $this->hasOne(MeterReading::class)->latestOfMany();
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeFaulty($query)
    {
        return $query->where('status', 'faulty');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('meter_category_id', $categoryId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('installation_address', 'like', "%{$location}%");
    }

    // Accessors
    public function getCurrentReadingAttribute()
    {
        return $this->latestReading ? $this->latestReading->current_reading : $this->initial_reading;
    }

    public function getLastReadingDateAttribute()
    {
        return $this->latestReading ? $this->latestReading->reading_date : null;
    }

    public function getTotalConsumptionAttribute()
    {
        return $this->meterReadings()->sum('consumption');
    }

    public function getIsAssignedAttribute()
    {
        return $this->status === 'assigned' && $this->customer_id !== null;
    }

    public function getCategoryNameAttribute()
    {
        return $this->meterCategory ? $this->meterCategory->name : 'Uncategorized';
    }

    public function getTotalChargesAttribute()
    {
        return $this->installation_fee + $this->connection_fee + $this->deposit_amount;
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->current_balance - $this->deposit_amount;
    }

    // Methods
    public function calculateBill($consumption)
    {
        if (!$this->meterCategory) {
            return 0;
        }

        $consumptionCharge = $this->meterCategory->calculateCharge($consumption);
        $baseCharge = $this->meterCategory->base_charge;
        $meterRent = $this->meterCategory->meter_rent;

        return $baseCharge + $meterRent + $consumptionCharge;
    }

    public function updateBalance($amount, $type = 'charge')
    {
        if ($type === 'charge') {
            $this->current_balance += $amount;
        } elseif ($type === 'payment') {
            $this->current_balance -= $amount;
        }
        
        $this->save();
    }

    // Get initial reading with date
    public function getInitialReadingWithDateAttribute()
    {
        $initialReading = $this->meterReadings()
            ->where('reading_type', 'initial')
            ->first();

        return $initialReading ? [
            'reading' => $initialReading->current_reading,
            'date' => $initialReading->reading_date
        ] : [
            'reading' => $this->initial_reading,
            'date' => $this->installation_date
        ];
    }
}