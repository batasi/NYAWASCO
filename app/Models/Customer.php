<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        return $this->hasOne(Meter::class)->latestOfMany();
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class)->orderBy('reading_date', 'desc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class)->orderBy('created_at', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date', 'desc');
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
        $requirements = $this->getActivationRequirements();
        return empty($requirements);
    }

    public function getActivationRequirements()
    {
        $requirements = [];

        if (!$this->meters()->exists()) {
            $requirements[] = 'No meter assigned';
        }

        // Fix: Check water application and files properly
        $waterApp = $this->waterApplication;

        if (!$waterApp) {
            $requirements[] = 'Water application missing';
        } else {
            // Check if files exist and are not empty strings
            if (empty($waterApp->national_id_file)) {
                $requirements[] = 'National ID document missing';
            } else {
                // Verify file actually exists in storage
                if (!Storage::disk('public')->exists($waterApp->national_id_file)) {
                    $requirements[] = 'National ID file not found in storage';
                }
            }

            if (empty($waterApp->kra_pin_file)) {
                $requirements[] = 'KRA PIN certificate missing';
            } else {
                if (!Storage::disk('public')->exists($waterApp->kra_pin_file)) {
                    $requirements[] = 'KRA PIN file not found in storage';
                }
            }
        }

        // FIX: Check for positive balance (debt)
        if ($this->current_balance > 0) {
            $requirements[] = 'Outstanding balance exists: ' . number_format($this->current_balance, 2);
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


    public function getArrearsAttribute()
    {
        // Arrears are unpaid bills that are overdue
        return $this->bills()
            ->where('bill_status', 'unpaid')
            ->where('due_date', '<', now())
            ->sum('total_amount');
    }



    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->plot_number,
            $this->house_number,
            $this->ward,
            $this->location
        ]);

        return implode(', ', $parts) ?: 'Address not specified';
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


    // Add to your Customer model
    public function getTotalConsumptionAttribute()
    {
        return $this->meterReadings->sum('consumption');
    }

    public function getAverageMonthlyConsumptionAttribute()
    {
        $readingsCount = $this->meterReadings->count();
        return $readingsCount > 0 ? $this->total_consumption / $readingsCount : 0;
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->bills()->where('bill_status', '!=', 'paid')->sum('total_amount');
    }

    // Add these scopes to your Bill model if needed
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('bill_status', '!=', 'paid');
    }

    // Get total consumption


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
        return $latestReading ? $latestReading->current_reading : ($this->meters()->first()->initial_reading ?? 0);
    }

    public function getLastReadingDateAttribute()
    {
        $latestReading = $this->meterReadings()->latest()->first();
        return $latestReading ? $latestReading->reading_date : null;
    }

    public function getCategoryNameAttribute()
    {
        $meter = $this->meters()->first();
        return $meter && $meter->meterCategory
            ? $meter->meterCategory->name
            : 'No Category';
    }

    public function getMeterNumberAttribute()
    {
        $meter = $this->meters()->first();
        return $meter ? $meter->meter_number : 'Not Assigned';
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
