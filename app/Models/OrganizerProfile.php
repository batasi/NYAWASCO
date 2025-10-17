<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo',
        'about',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'tax_id',
        'business_registration_number',
        'social_links',
        'business_hours',
        'is_verified',
        'is_featured',
        'rating',
        'total_events',
        'total_voting_contests',
        'total_attendees',
        'total_revenue',
        'metadata',
    ];

    protected $casts = [
        'social_links' => 'array',
        'business_hours' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
        'total_events' => 'integer',
        'total_voting_contests' => 'integer',
        'total_attendees' => 'integer',
        'total_revenue' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id', 'user_id');
    }

    public function votingContests()
    {
        return $this->hasMany(VotingContest::class, 'organizer_id', 'user_id');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
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

    public function scopePopular($query)
    {
        return $query->orderBy('total_events', 'desc')
            ->orderBy('rating', 'desc');
    }

    // Methods
    public function getAverageRatingAttribute()
    {
        return $this->rating > 0 ? $this->rating / 20 : 0; // Assuming rating is stored as integer * 20
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]));
    }

    public function incrementEventsCount()
    {
        $this->increment('total_events');
    }

    public function decrementEventsCount()
    {
        $this->decrement('total_events');
    }

    public function incrementVotingContestsCount()
    {
        $this->increment('total_voting_contests');
    }

    public function decrementVotingContestsCount()
    {
        $this->decrement('total_voting_contests');
    }

    public function updateAttendeesCount()
    {
        $this->total_attendees = $this->events()
            ->join('ticket_purchases', 'events.id', '=', 'ticket_purchases.event_id')
            ->where('ticket_purchases.status', 'paid')
            ->sum('ticket_purchases.quantity');
        $this->save();
    }

    public function updateRevenue()
    {
        $this->total_revenue = $this->events()
            ->join('ticket_purchases', 'events.id', '=', 'ticket_purchases.event_id')
            ->where('ticket_purchases.status', 'paid')
            ->sum('ticket_purchases.final_amount');
        $this->save();
    }

    public function getSocialLink($platform)
    {
        return $this->social_links[$platform] ?? null;
    }

    public function isOpenNow()
    {
        if (empty($this->business_hours)) {
            return true;
        }

        $currentDay = strtolower(now()->englishDayOfWeek);
        $currentTime = now()->format('H:i');

        $todayHours = $this->business_hours[$currentDay] ?? null;

        if (!$todayHours || ($todayHours['closed'] ?? false)) {
            return false;
        }

        return $currentTime >= $todayHours['open'] && $currentTime <= $todayHours['close'];
    }
}
