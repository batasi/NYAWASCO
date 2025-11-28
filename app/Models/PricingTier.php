<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_category_id',
        'name',
        'min_consumption',
        'max_consumption',
        'rate_per_unit',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_consumption' => 'decimal:2',
        'max_consumption' => 'decimal:2',
        'rate_per_unit' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function meterCategory()
    {
        return $this->belongsTo(MeterCategory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('min_consumption');
    }

    // Accessors
    public function getConsumptionRangeAttribute()
    {
        if ($this->max_consumption) {
            return $this->min_consumption . ' - ' . $this->max_consumption . ' m³';
        }
        return $this->min_consumption . ' m³ and above';
    }

    public function getFormattedRateAttribute()
    {
        return 'KSh ' . number_format($this->rate_per_unit, 4) . '/m³';
    }
}