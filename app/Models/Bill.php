<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meter_id',
        'bill_number',
        'billing_period_start',
        'billing_period_end',
        'consumption',
        'base_charge',
        'consumption_charge',
        'tax_amount',
        'late_fee',
        'total_amount',
        'due_date',
        'bill_status',
        'payment_date',
        'notes',
        'created_by',
    ];
    // app/Models/Bill.php
    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
    ];

    // Relationships

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Bill.php
public function user() {
    return $this->belongsTo(User::class, 'user_id');
}

public function payments() {
    return $this->hasMany(Payment::class);
}

}
