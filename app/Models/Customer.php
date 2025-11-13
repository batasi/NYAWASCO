<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'physical_address',
        'postal_address',
        'meter_number',
        'meter_type',
        'connection_type',
        'connection_date',
        'status',
        'initial_meter_reading',
        'initial_reading_date',
        'notes',
    ];

    protected $casts = [
        'connection_date' => 'date',
        'initial_reading_date' => 'date',
        'initial_meter_reading' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = 'CUST' . date('Ymd') . str_pad(static::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);
            }
            
            // Auto-generate meter number if not provided
            if (empty($customer->meter_number)) {
                $customer->meter_number = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($customer) {
            // Create initial meter reading record
            if ($customer->initial_meter_reading && $customer->initial_reading_date) {
                MeterReading::create([
                    'customer_id' => $customer->id,
                    'current_reading' => $customer->initial_meter_reading,
                    'previous_reading' => 0,
                    'consumption' => $customer->initial_meter_reading,
                    'reading_date' => $customer->initial_reading_date,
                    'reading_type' => 'initial',
                    'reading_period' => 'Initial Reading',
                    'billed' => false,
                    'read_by' => auth()->id() ?? 1,
                    'notes' => 'Initial meter reading upon registration',
                ]);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        return $this->physical_address . ($this->postal_address ? "\n" . $this->postal_address : '');
    }

    // Relationships
    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class)->orderBy('reading_date', 'desc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class)->orderBy('billing_date', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestMeterReading()
    {
        return $this->hasOne(MeterReading::class)->latestOfMany();
    }

    public function currentBillingPeriodReading()
    {
        $currentPeriod = now()->format('F Y');
        return $this->hasOne(MeterReading::class)->where('reading_period', $currentPeriod);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithUnbilledReadings($query)
    {
        return $query->whereHas('meterReadings', function ($q) {
            $q->where('billed', false)
              ->where('reading_type', 'monthly');
        });
    }

    public function getCurrentConsumptionAttribute()
    {
        $latestReading = $this->latestMeterReading;
        return $latestReading ? $latestReading->consumption : 0;
    }

    public function getLastBillingDateAttribute()
    {
        $lastBill = $this->bills()->latest()->first();
        return $lastBill ? $lastBill->billing_date : null;
    }

    // New methods for meter management
    public function scopeWithMeterNumber($query, $meterNumber)
    {
        return $query->where('meter_number', $meterNumber);
    }

    public static function getAvailableMeterTypes()
    {
        return [
            'domestic' => 'Domestic',
            'commercial' => 'Commercial', 
            'industrial' => 'Industrial',
            'institutional' => 'Institutional',
        ];
    }


    public function getInstallationAddressAttribute()
    {
        return $this->physical_address;
    }

    public static function getConnectionTypes()
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'public' => 'Public Institution',
        ];
    }

    public function canHaveMeterAssigned()
    {
        return empty($this->meter_number) || $this->meter_number === 'PENDING';
    }
}