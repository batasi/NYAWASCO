<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WriteOff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'amount',
        'type',
        'reason',
        'description',
        'write_off_date',
        'status',
        'approved_by',
        'approved_at',
        'affected_bills'
    ];

    protected $casts = [
        'affected_bills' => 'array',
        'write_off_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reversed_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'reversed' => 'gray',
            default => 'gray'
        };
    }
}
