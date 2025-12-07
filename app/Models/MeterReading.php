<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'meter_id',
        'current_reading',
        'previous_reading',
        'consumption',
        'reading_date',
        'reading_type',
        'reading_status', // NEW
        'exception_type', // NEW
        'exception_reason', // NEW
        'estimated', // NEW
        'estimated_consumption', // NEW
        'exception_evidence', // NEW
        'reading_period',
        'billed',
        'billed_by',
        'billed_at',
        'notes',
        'reading_image',
        'read_by',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'current_reading' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'consumption' => 'decimal:2',
        'estimated_consumption' => 'decimal:2',
        'estimated' => 'boolean',
        'billed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reading) {
            // Auto-calculate consumption if not an exception
            if ($reading->reading_status === 'recorded' && !$reading->estimated) {
                $reading->consumption = $reading->current_reading - $reading->previous_reading;
            }
            
            // Auto-set reading period if not provided
            if (empty($reading->reading_period)) {
                $reading->reading_period = \Carbon\Carbon::parse($reading->reading_date)->format('F Y');
            }
        });

        static::updating(function ($reading) {
            // Recalculate consumption if readings change and it's a normal reading
            if ($reading->isDirty(['current_reading', 'previous_reading']) && 
                $reading->reading_status === 'recorded' && 
                !$reading->estimated) {
                $reading->consumption = $reading->current_reading - $reading->previous_reading;
            }
        });
    }

    // Constants for reading statuses
    const STATUS_RECORDED = 'recorded';
    const STATUS_EXCEPTION = 'exception';
    const STATUS_ESTIMATED = 'estimated';

    // Constants for exception types
    const EXCEPTION_INACCESSIBLE = 'inaccessible';
    const EXCEPTION_FAULTY = 'faulty';
    const EXCEPTION_STUCK = 'stuck';
    const EXCEPTION_DAMAGED = 'damaged';
    const EXCEPTION_VANDALIZED = 'vandalized';
    const EXCEPTION_OTHER = 'other';

    // Add this to allow null customer_id
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['customer_id'] = $this->attributes['customer_id'] ?? null;
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function biller()
    {
        return $this->belongsTo(User::class, 'billed_by');
    }

    public function bill()
    {
        return $this->hasOne(Bill::class, 'meter_reading_id');
    }

    // Scopes
    public function scopeMonthly($query)
    {
        return $query->where('reading_type', 'monthly');
    }

    public function scopeBilled($query)
    {
        return $query->where('billed', true);
    }

    public function scopeUnbilled($query)
    {
        return $query->where('billed', false);
    }

    public function scopeForPeriod($query, $period)
    {
        return $query->where('reading_period', $period);
    }

    public function scopeCurrentPeriod($query)
    {
        return $query->where('reading_period', now()->format('F Y'));
    }

    public function scopeForMeter($query, $meterId)
    {
        return $query->where('meter_id', $meterId);
    }

    // Exception scopes
    public function scopeExceptions($query)
    {
        return $query->where('reading_status', self::STATUS_EXCEPTION);
    }

    public function scopeEstimated($query)
    {
        return $query->where('reading_status', self::STATUS_ESTIMATED);
    }

    public function scopeNormal($query)
    {
        return $query->where('reading_status', self::STATUS_RECORDED);
    }

    public function scopeWithException($query, $exceptionType = null)
    {
        $query = $query->where('reading_status', self::STATUS_EXCEPTION);
        if ($exceptionType) {
            $query->where('exception_type', $exceptionType);
        }
        return $query;
    }

    // Helper methods
    public function getFormattedConsumptionAttribute()
    {
        if ($this->estimated) {
            return number_format($this->estimated_consumption, 2) . ' m³ (Estimated)';
        }
        return number_format($this->consumption, 2) . ' m³';
    }

    public function isBillable()
    {
        return !$this->billed && 
               $this->reading_type === 'monthly' && 
               ($this->consumption > 0 || $this->estimated_consumption > 0);
    }

    public function getMeterNumberAttribute()
    {
        return $this->meter ? $this->meter->meter_number : 'N/A';
    }

    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->first_name . ' ' . $this->customer->last_name : 'N/A';
    }

    public function isException()
    {
        return $this->reading_status === self::STATUS_EXCEPTION;
    }

    public function isEstimated()
    {
        return $this->reading_status === self::STATUS_ESTIMATED;
    }

    public function isNormal()
    {
        return $this->reading_status === self::STATUS_RECORDED;
    }

    public function getExceptionTypeTextAttribute()
    {
        $types = [
            self::EXCEPTION_INACCESSIBLE => 'Meter Inaccessible',
            self::EXCEPTION_FAULTY => 'Meter Faulty',
            self::EXCEPTION_STUCK => 'Meter Stuck',
            self::EXCEPTION_DAMAGED => 'Meter Damaged',
            self::EXCEPTION_VANDALIZED => 'Meter Vandalized',
            self::EXCEPTION_OTHER => 'Other',
        ];

        return $types[$this->exception_type] ?? 'Unknown';
    }

    public function getReadingStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_RECORDED => 'Recorded',
            self::STATUS_EXCEPTION => 'Exception',
            self::STATUS_ESTIMATED => 'Estimated',
        ];

        return $statuses[$this->reading_status] ?? 'Unknown';
    }

    // Method to calculate estimated consumption based on history
    public function calculateEstimation()
    {
        if (!$this->meter || !$this->customer) {
            return 0;
        }

        // Get last 3 normal readings
        $previousReadings = self::where('customer_id', $this->customer_id)
            ->where('meter_id', $this->meter_id)
            ->where('reading_status', self::STATUS_RECORDED)
            ->where('billed', true)
            ->orderBy('reading_date', 'desc')
            ->limit(3)
            ->get();

        if ($previousReadings->isEmpty()) {
            return $this->meter->meterCategory->default_rate * 10; // Default estimation
        }

        // Calculate average consumption
        $averageConsumption = $previousReadings->avg('consumption');
        
        return round($averageConsumption, 2);
    }
}