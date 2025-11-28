<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterConnectionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'kra_pin',
        'kra_pin_file',
        'national_id',
        'national_id_file',
        'plot_number',
        'house_number',
        'estate',
        'expected_users',
        'property_owner',
        'title_document',
        'signature',
        'date',
        'status',
        'decline_reason',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'processed_at' => 'datetime',
        'expected_users' => 'integer',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }

    // Attributes
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        return $this->plot_number . ', ' . $this->house_number . ($this->estate ? ', ' . $this->estate : '');
    }
}