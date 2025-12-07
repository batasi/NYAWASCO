<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use HasFactory, SoftDeletes;
       // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_ACTIVE = 'active';
    const STATUS_FAULTY = 'faulty';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'meter_number',
        'meter_type',
        'meter_model',
        'longtitude',
        'latitude',
        'meter_category_id',
        'status',
        'customer_id',
        'zone_id',
        'walk_route_id',
        'installation_address',
        'installation_date',
        'last_maintenance_date',
        'initial_reading',
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

        // Sync customer status when meter status changes
        static::updated(function ($meter) {
            if ($meter->isDirty('status') && $meter->customer) {
                $meter->customer->syncStatusFromMeters();
            }
        });

        // Sync customer status when meter is assigned/unassigned
        static::saved(function ($meter) {
            if ($meter->isDirty('customer_id') && ($meter->customer || $meter->getOriginal('customer_id'))) {
                if ($meter->customer) {
                    $meter->customer->syncStatusFromMeters();
                }
                // Also sync the previous customer if unassigned
                if ($meter->getOriginal('customer_id') && !$meter->customer_id) {
                    $previousCustomer = Customer::find($meter->getOriginal('customer_id'));
                    if ($previousCustomer) {
                        $previousCustomer->syncStatusFromMeters();
                    }
                }
            }
        });
    }

    // Relationships
    public function zone() {
        return $this->belongsTo(Zone::class);
    }

    public function walkroute() {
        return $this->belongsTo(WalkRoute::class);
    }
 public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Add this if you want more status scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function meterCategory()
    {
        return $this->belongsTo(MeterCategory::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class)->orderBy('reading_date', 'desc');
    }

    public function latestReading()
    {
        return $this->hasOne(MeterReading::class)
            ->latestOfMany()
            ->select('meter_readings.*'); // IMPORTANT FIX
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
        return $query->where('status', 'activeS');
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
        return $this->status === 'active' && $this->customer_id !== null;
    }

    public function getCategoryNameAttribute()
    {
        return $this->meterCategory ? $this->meterCategory->name : 'Uncategorized';
    }

    public function getTotalChargesAttribute()
    {
        return 0;
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

    /**
     * Search scope for meters
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function($q) use ($searchTerm) {
            $q->where('meter_number', 'LIKE', "%{$searchTerm}%")
            ->orWhere('meter_model', 'LIKE', "%{$searchTerm}%")
            ->orWhere('installation_address', 'LIKE', "%{$searchTerm}%")
            ->orWhereHas('customer', function($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('customer_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('estate', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('plot_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('house_number', 'LIKE', "%{$searchTerm}%");
            });
        });
    }

    /**
     * Scope for status filter
     */
    public function scopeOfStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope for meter type filter
     */
    public function scopeOfType($query, $type)
    {
        if (empty($type)) {
            return $query;
        }

        return $query->where('meter_type', $type);
    }

    /**
     * Scope for balance filter
     */
    public function scopeOfBalance($query, $balanceFilter)
    {
        switch ($balanceFilter) {
            case 'positive':
                return $query->where('current_balance', '>', 0);
            case 'negative':
                return $query->where('current_balance', '<', 0);
            case 'zero':
                return $query->where('current_balance', '=', 0);
            case 'overdue':
                return $query->where('current_balance', '>', 1000);
            default:
                return $query;
        }
    }

    /**
     * Optimized scope for minimal data loading
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'customer' => function($q) {
                $q->select('id', 'first_name', 'last_name', 'customer_number', 'estate', 'plot_number', 'house_number');
            },
            'meterCategory' => function($q) {
                $q->select('id', 'name', 'code');
            }
        ]);
    }
}
