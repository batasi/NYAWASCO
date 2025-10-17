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
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function purchases()
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
        return $query->where(function ($q) {
            $q->whereNull('quantity_available')
                ->orWhereRaw('quantity_available > quantity_sold');
        });
    }

    // Methods
    public function getAvailableQuantityAttribute()
    {
        if (is_null($this->quantity_available)) {
            return null; // Unlimited
        }
        return max(0, $this->quantity_available - $this->quantity_sold);
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

        if (!is_null($this->available_quantity) && $this->available_quantity <= 0) {
            return false;
        }

        return true;
    }
}
