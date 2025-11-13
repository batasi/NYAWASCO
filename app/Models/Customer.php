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
        'plot_number',
        'house_number',
        'estate',
        'meter_number',
        'meter_type',
        'connection_type',
        'initial_meter_reading',
        'connection_date',
        'status',
        'kra_pin',
        'property_owner',
        'expected_users',
        'notes',
    ];

    protected $casts = [
        'connection_date' => 'date',
        'initial_meter_reading' => 'decimal:2',
        'expected_users' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = 'CUST' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function waterApplication()
    {
        return $this->hasOne(WaterConnectionApplication::class);
    }

    public function meter()
    {
        return $this->hasOne(Meter::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->plot_number . ', ' . $this->house_number;
        if ($this->estate) {
            $address .= ', ' . $this->estate;
        }
        return $address;
    }

    public function getCurrentMeterReadingAttribute()
    {
        $latestReading = $this->meterReadings()->latest()->first();
        return $latestReading ? $latestReading->current_reading : $this->initial_meter_reading;
    }

    public function getLastReadingDateAttribute()
    {
        $latestReading = $this->meterReadings()->latest()->first();
        return $latestReading ? $latestReading->reading_date : null;
    }

    // Static methods for dropdowns
    public static function getConnectionTypes()
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'public' => 'Public Institution',
        ];
    }

    public static function getAvailableMeterTypes()
    {
        return [
            'domestic' => 'Domestic - Single Phase',
            'commercial' => 'Commercial - Three Phase',
            'industrial' => 'Industrial - High Capacity',
            'institutional' => 'Institutional - Bulk Meter',
        ];
    }
}