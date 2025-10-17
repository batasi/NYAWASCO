@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Discover Events
                        @if(isset($category))
                        <span class="text-blue-600">in {{ $category->name }}</span>
                        @endif
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Find amazing events happening near you
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('organizer.events.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Event
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by event name, location..."
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ isset($category) && $category->id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" placeholder="City, Country..."
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" id="date_from"
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" id="date_to"
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md">
                </div>
                <div class="flex items-end">
                    <button type="button" id="applyFilters" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-900">
                    @if(isset($category))
                    {{ $category->name }} Events
                    @else
                    All Events
                    @endif
                    <span class="text-lg text-gray-600">({{ $events->total() }} found)</span>
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Sort by:</span>
                    <select id="sort" name="sort" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="date_asc">Date (Earliest)</option>
                        <option value="date_desc">Date (Latest)</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low to High)</option>
                        <option value="price_desc">Price (High to Low)</option>
                    </select>
                </div>
            </div>

            @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <!-- Event Image -->
                    <div class="relative h-48 bg-gray-200">
                        <img src="{{ $event->featured_image ? Storage::url($event->featured_image) : 'https://via.placeholder.com/400x200?text=Event+Image' }}"
                            alt="{{ $event->name }}"
                            class="w-full h-full object-cover">
                        @if($event->is_featured)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Featured
                            </span>
                        </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $event->category->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Event Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                    {{ $event->name }}
                                </a>
                            </h3>
                        </div>

                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $event->short_description ?? Str::limit($event->description, 100) }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $event->start_date->format('M j, Y g:i A') }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $event->venue_name }}, {{ $event->city }}
                            </div>
                        </div>

                        <!-- Price and Action -->
                        <div class="flex items-center justify-between">
                            <div>
                                @if($event->is_free)
                                <span class="text-lg font-bold text-green-600">Free</span>
                                @else
                                <span class="text-lg font-bold text-gray-900">{{ $event->currency }} {{ number_format($event->price, 2) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('events.show', $event) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View Details
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-xs text-gray-500">
                            <span>{{ $event->total_tickets_sold }} attendees</span>
                            <span>{{ $event->views_count }} views</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $events->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No events found</h3>
                <p class="mt-2 text-sm text-gray-600">
                    @if(isset($category))
                    There are no {{ $category->name }} events matching your criteria.
                    @else
                    There are no events matching your criteria. Try adjusting your filters.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('organizer.events') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear Filters
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Categories Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Browse by Category</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $cat)
                <a href="{{ route('events.byCategory', $cat) }}"
                    class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors duration-200 group">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2 group-hover:bg-blue-200">
                        @if($cat->icon)
                        <span class="text-lg">{{ $cat->icon }}</span>
                        @else
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-gray-700 text-center group-hover:text-blue-700">{{ $cat->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const applyFilters = document.getElementById('applyFilters');
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category');
        const locationInput = document.getElementById('location');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const sortSelect = document.getElementById('sort');

        function applyEventFilters() {
            const params = new URLSearchParams();

            if (searchInput.value) params.append('search', searchInput.value);
            if (categorySelect.value) params.append('category', categorySelect.value);
            if (locationInput.value) params.append('location', locationInput.value);
            if (dateFromInput.value) params.append('date_from', dateFromInput.value);
            if (dateToInput.value) params.append('date_to', dateToInput.value);
            if (sortSelect.value) params.append('sort', sortSelect.value);

            window.location.href = '{{ route('organizer.events') }}?' + params.toString();
        }

        applyFilters.addEventListener('click', applyEventFilters);

        // Enter key support for search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyEventFilters();
            }
        });

        // Auto-apply sort
        sortSelect.addEventListener('change', applyEventFilters);
    });
</script>

<style>
    .line-clamp-1 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
    }

    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
</style>
@endsection
