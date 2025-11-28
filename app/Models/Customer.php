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
        'balance_bf', // Balance brought forward
        'current_balance', // Current account balance
        'notes',
    ];

    protected $casts = [
        'connection_date' => 'date',
        'initial_meter_reading' => 'decimal:2',
        'expected_users' => 'integer',
        'balance_bf' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = 'CUST' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
            
            // Set default status if not provided
            if (empty($customer->status)) {
                $customer->status = 'new';
            }
        });

        static::updating(function ($customer) {
            // Update current balance when balance_bf changes
            if ($customer->isDirty('balance_bf')) {
                $customer->current_balance = $customer->balance_bf;
            }
        });
    }

    // Relationships
    public function waterApplication()
    {
        return $this->hasOne(WaterConnectionApplication::class);
    }

    public function meters()
{
    return $this->hasMany(Meter::class);
}
    public function meter()
    {
        return $this->hasOne(Meter::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class)->orderBy('reading_date', 'desc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'customer_id')->orderBy('created_at', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id')->orderBy('payment_date', 'desc');
    }

    // Status Management Methods
    public function markAsActive()
    {
        if ($this->canBeActivated()) {
            $this->update(['status' => 'active']);
            return true;
        }
        return false;
    }

    public function markAsSealed()
    {
        $this->update(['status' => 'sealed']);
        return true;
    }

    public function markAsTerminated()
    {
        $this->update(['status' => 'terminated']);
        return true;
    }

    public function markAsPendingPayment()
    {
        $this->update(['status' => 'pending_payment']);
        return true;
    }

    public function canBeActivated()
    {
        // Check if customer has all requirements to be activated
        $hasMeter = $this->meter()->exists();
        $hasValidDocuments = $this->waterApplication && 
                            $this->waterApplication->national_id_file && 
                            $this->waterApplication->kra_pin_file;
        
        return $hasMeter && $hasValidDocuments && $this->current_balance >= 0;
    }

    public function getActivationRequirements()
    {
        $requirements = [];

        if (!$this->meter()->exists()) {
            $requirements[] = 'No meter assigned';
        }

        if (!$this->waterApplication || !$this->waterApplication->national_id_file) {
            $requirements[] = 'National ID document missing';
        }

        if (!$this->waterApplication || !$this->waterApplication->kra_pin_file) {
            $requirements[] = 'KRA PIN certificate missing';
        }

        if ($this->current_balance < 0) {
            $requirements[] = 'Outstanding balance exists';
        }

        return $requirements;
    }

    // Balance Calculations
    public function getAccountBalanceAttribute()
    {
        $totalPaid = $this->payments()->sum('amount');
        $totalBilled = $this->bills()->sum('total_amount');
        return $totalPaid - $totalBilled;
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->bills()->where('bill_status', 'unpaid')->sum('total_amount');
    }

    public function getArrearsAttribute()
    {
        // Arrears are unpaid bills that are overdue
        return $this->bills()
            ->where('bill_status', 'unpaid')
            ->where('due_date', '<', now())
            ->sum('total_amount');
    }

    public function getCurrentBalanceAttribute()
    {
        // Use the stored current_balance, but calculate if needed
        if ($this->attributes['current_balance'] === null) {
            return $this->balance_bf + $this->account_balance;
        }
        return $this->attributes['current_balance'];
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

    // Get total consumption
    public function getTotalConsumptionAttribute()
    {
        return $this->meterReadings()->sum('consumption');
    }

    // Get average monthly consumption
    public function getAverageMonthlyConsumptionAttribute()
    {
        $readingsCount = $this->meterReadings()->count();
        if ($readingsCount === 0) return 0;
        
        return $this->total_consumption / $readingsCount;
    }

    // Get recent bills (last 6 months)
    public function recentBills($limit = 6)
    {
        return $this->bills()->with('payments')->latest()->limit($limit)->get();
    }

    // Get payment history
    public function paymentHistory($limit = 10)
    {
        return $this->payments()->latest()->limit($limit)->get();
    }

    // Status-based Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeSealed($query)
    {
        return $query->where('status', 'sealed');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInactive($query)
    {
        return $query->whereIn('status', ['sealed', 'terminated']);
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

    public function getCategoryNameAttribute()
    {
        return $this->meter && $this->meter->meterCategory 
            ? $this->meter->meterCategory->name 
            : 'No Category';
    }

    public function getMeterNumberAttribute()
    {
        return $this->meter ? $this->meter->meter_number : 'Not Assigned';
    }

    // Static methods for dropdowns
    public static function getStatuses()
    {
        return [
            'new' => 'New',
            'active' => 'Active',
            'pending_payment' => 'Pending Payment',
            'sealed' => 'Sealed',
            'terminated' => 'Terminated',
        ];
    }

    public static function getStatusColors()
    {
        return [
            'new' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'pending_payment' => 'bg-yellow-100 text-yellow-800',
            'sealed' => 'bg-red-100 text-red-800',
            'terminated' => 'bg-gray-100 text-gray-800',
        ];
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