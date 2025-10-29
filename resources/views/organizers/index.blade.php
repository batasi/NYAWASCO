@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Event Organizers
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Discover talented organizers creating amazing events in your community
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    @auth
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                My Organizer Dashboard
                            </a>
                        @else
                            <a href="{{ route('organizer.dashboard') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Become an Organizer
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Organizers</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by organizer name, company..."
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" placeholder="City, Country..."
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="verified" class="block text-sm font-medium text-gray-700 mb-2">Verification</label>
                    <select id="verified" name="verified" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="all">All Organizers</option>
                        <option value="verified">Verified Only</option>
                        <option value="featured">Featured Only</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort" name="sort" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="popular">Most Popular</option>
                        <option value="newest">Newest First</option>
                        <option value="events">Most Events</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" id="applyFilters" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Organizers Grid -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-900">
                    All Organizers
                    <span class="text-lg text-gray-600">({{ $organizers->total() }} found)</span>
                </h2>
            </div>

            @if($organizers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($organizers as $organizer)
                        @php
                            $organizerProfile = $organizer->organizerProfile;
                        @endphp
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Organizer Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                                <div class="flex items-center space-x-4">
                                    <!-- Organizer Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($organizer->avatar)
                                            <img src="{{ Storage::url($organizer->avatar) }}"
                                                 alt="{{ $organizer->name }}"
                                                 class="w-16 h-16 rounded-full object-cover border-2 border-white">
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-white text-xl font-bold border-2 border-white">
                                                {{ strtoupper(substr($organizer->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Organizer Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold truncate">{{ $organizer->name }}</h3>
                                        @if($organizerProfile && $organizerProfile->company_name)
                                            <p class="text-blue-100 text-sm truncate">{{ $organizerProfile->company_name }}</p>
                                        @endif

                                        <!-- Badges -->
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @if($organizerProfile && $organizerProfile->is_verified)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Verified
                                                </span>
                                            @endif
                                            @if($organizerProfile && $organizerProfile->is_featured)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Organizer Details -->
                            <div class="p-6">
                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 mb-4 text-center">
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ $organizer->organized_events_count }}</div>
                                        <div class="text-xs text-gray-500">Events</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ $organizer->organized_voting_contests_count }}</div>
                                        <div class="text-xs text-gray-500">Voting</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">
                                            @if($organizerProfile && $organizerProfile->rating > 0)
                                                {{ number_format($organizerProfile->rating / 20, 1) }}/5
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">Rating</div>
                                    </div>
                                </div>

                                <!-- Location -->
                                @if($organizerProfile && ($organizerProfile->city || $organizerProfile->country))
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $organizerProfile->city }}{{ $organizerProfile->city && $organizerProfile->country ? ', ' : '' }}{{ $organizerProfile->country }}
                                    </div>
                                @endif

                                <!-- About -->
                                @if($organizerProfile && $organizerProfile->about)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ Str::limit($organizerProfile->about, 100) }}
                                    </p>
                                @else
                                    <p class="text-gray-400 text-sm mb-4 italic">
                                        No description provided
                                    </p>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('organizers.show', $organizer) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View Profile
                                    </a>
                                    <a href="{{ route('events.index', ['organizer' => $organizer->id]) }}"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View Events
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $organizers->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No organizers found</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        There are no organizers matching your criteria. Try adjusting your filters.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('organizers.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Become an Organizer CTA -->
        @guest
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-lg shadow-sm p-8 text-center text-white">
                <h3 class="text-2xl font-bold mb-4">Become an Organizer</h3>
                <p class="text-blue-100 text-lg mb-6 max-w-2xl mx-auto">
                    Join our community of event organizers and start creating unforgettable experiences for your audience.
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Sign Up Now
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        Learn More
                    </a>
                </div>
            </div>
        @endguest
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const applyFilters = document.getElementById('applyFilters');
    const searchInput = document.getElementById('search');
    const locationInput = document.getElementById('location');
    const verifiedSelect = document.getElementById('verified');
    const sortSelect = document.getElementById('sort');

    function applyOrganizerFilters() {
        const params = new URLSearchParams();

        if (searchInput.value) params.append('search', searchInput.value);
        if (locationInput.value) params.append('location', locationInput.value);
        if (verifiedSelect.value !== 'all') params.append('verified', verifiedSelect.value);
        if (sortSelect.value !== 'popular') params.append('sort', sortSelect.value);

        window.location.href = '{{ route('organizers.index') }}?' + params.toString();
    }

    applyFilters.addEventListener('click', applyOrganizerFilters);

    // Enter key support for search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyOrganizerFilters();
        }
    });

    // Auto-apply sort
    sortSelect.addEventListener('change', applyOrganizerFilters);
});
</script>

<style>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>
@endsection
