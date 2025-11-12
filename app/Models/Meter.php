<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_number',
        'meter_type',
        'status',
        'customer_id',
        'installation_date',
        'notes'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meter) {
            if (empty($meter->meter_number)) {
                $meter->meter_number = 'MTR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function readings()
    {
        return $this->hasMany(MeterReading::class);
    }

    // Scope to get available meters
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Scope to get assigned meters
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }
}