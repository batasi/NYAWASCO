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
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'featured_image',
        'gallery_images',
        'venue_name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'max_attendees',
        'price',
        'currency',
        'is_free',
        'is_featured',
        'is_active',
        'requires_approval',
        'status',
        'tags',
        'metadata',
        'views_count',
        'bookmarks_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'gallery_images' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByLocation($query, $city = null, $country = null)
    {
        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($country) {
            $query->where('country', 'like', "%{$country}%");
        }
        return $query;
    }

    // Methods
    public function getTotalTicketsSoldAttribute()
    {
        return $this->ticketPurchases()->where('status', 'paid')->sum('quantity');
    }

    public function getAvailableTicketsAttribute()
    {
        if (is_null($this->max_attendees)) {
            return null; // Unlimited
        }
        return max(0, $this->max_attendees - $this->total_tickets_sold);
    }

    public function isSoldOut()
    {
        if (is_null($this->max_attendees)) {
            return false;
        }
        return $this->total_tickets_sold >= $this->max_attendees;
    }

    public function isRegistrationOpen()
    {
        $now = now();
        return $this->registration_start <= $now &&
            (is_null($this->registration_end) || $this->registration_end >= $now);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
