<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'base_charge',
        'meter_rent',
        'has_tiers',
        'default_rate',
        'is_active',
        'sort_order',
        'additional_charges',
    ];

    protected $casts = [
        'base_charge' => 'decimal:2',
        'meter_rent' => 'decimal:2',
        'default_rate' => 'decimal:4',
        'has_tiers' => 'boolean',
        'is_active' => 'boolean',
        'additional_charges' => 'array',
    ];

    // Relationships
    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class)->orderBy('min_consumption');
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function activePricingTiers()
    {
        return $this->pricingTiers()->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function calculateCharge($consumption)
    {
        if (!$this->has_tiers) {
            return $consumption * $this->default_rate;
        }

        $totalCharge = 0;
        $remainingConsumption = $consumption;

        foreach ($this->activePricingTiers as $tier) {
            if ($remainingConsumption <= 0) break;

            $tierRange = $tier->max_consumption 
                ? min($tier->max_consumption - $tier->min_consumption, $remainingConsumption)
                : $remainingConsumption;

            if ($tierRange > 0) {
                $tierConsumption = min($tierRange, $remainingConsumption);
                $totalCharge += $tierConsumption * $tier->rate_per_unit;
                $remainingConsumption -= $tierConsumption;
            }
        }

        return $totalCharge;
    }

    public function getTierForConsumption($consumption)
    {
        if (!$this->has_tiers) {
            return null;
        }

        return $this->activePricingTiers
            ->where('min_consumption', '<=', $consumption)
            ->where(function($query) use ($consumption) {
                $query->where('max_consumption', '>=', $consumption)
                      ->orWhereNull('max_consumption');
            })
            ->first();
    }
}