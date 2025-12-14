<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $table = 'payment_allocations';

    protected $fillable = [
        'payment_id',
        'bill_id',
        'amount',
        'allocated_to_principal',
        'allocated_to_late_fee',
        'allocation_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'allocated_to_principal' => 'decimal:2',
        'allocated_to_late_fee' => 'decimal:2',
        'allocation_date' => 'datetime',
    ];

    /* =======================
     |  Relationships
     |=======================*/

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
