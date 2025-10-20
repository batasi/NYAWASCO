<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quantity_available',
        'quantity_sold',
        'max_per_order',
        'sale_start_date',
        'sale_end_date',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity_available' => 'integer',
        'quantity_sold' => 'integer',
        'max_per_order' => 'integer',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'quantity_sold' => 0,
        'max_per_order' => 10,
        'is_active' => true,
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity_available', '>', 0)
            ->orWhereNull('quantity_available');
    }

    public function scopeOnSale($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('sale_start_date')
                ->orWhere('sale_start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('sale_end_date')
                ->orWhere('sale_end_date', '>=', $now);
        });
    }

    // Helper Methods
    public function getAvailableQuantityAttribute()
    {
        if (is_null($this->quantity_available)) {
            return null; // Unlimited tickets
        }
        return $this->quantity_available - $this->quantity_sold;
    }

    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->sale_start_date && $this->sale_start_date > $now) {
            return false;
        }

        if ($this->sale_end_date && $this->sale_end_date < $now) {
            return false;
        }

        return is_null($this->available_quantity) || $this->available_quantity > 0;
    }

    public function canPurchase($quantity = 1)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($quantity > $this->max_per_order) {
            return false;
        }

        return is_null($this->available_quantity) || $this->available_quantity >= $quantity;
    }
}
