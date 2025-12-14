<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meter_id',
        'user_id',
        'bill_id',
        'customer_id',
        'payment_no',
        'balance',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'payment_status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Payment belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Payment belongs to a meter.
     */
    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    /**
     * Payment belongs to a bill (optional - through meter)
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get customer through meter
     */
    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Meter::class, 'id', 'id', 'meter_id', 'customer_id');
    }
    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

}
