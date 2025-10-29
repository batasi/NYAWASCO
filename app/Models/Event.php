<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'ticket_price',
        'capacity',
        'is_active',
        'is_featured',
        'status',
        'banner_image',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'ticket_price' => 'decimal:2',
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    protected $attributes = [
        'status' => 'draft',
        'is_active' => true,
    ];

    // Relationships
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Helper Methods
    public function getTotalTicketsSoldAttribute()
    {
        return $this->ticketPurchases()->where('status', 'paid')->sum('quantity');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->ticketPurchases()->where('status', 'paid')->sum('final_amount');
    }

    public function getAvailableTicketsAttribute()
    {
        return $this->capacity - $this->total_tickets_sold;
    }

    public function isSoldOut()
    {
        return $this->available_tickets <= 0;
    }

    public function canPurchaseTickets()
    {
        return $this->is_active &&
            $this->status === 'approved' &&
            !$this->isSoldOut() &&
            $this->start_date > now();
    }
}
