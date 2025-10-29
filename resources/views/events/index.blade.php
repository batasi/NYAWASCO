@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="min-h-screen modal-header">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        Discover Events
                        @if(isset($category))
                        <span class="text-blue-600">in {{ $category->name }}</span>
                        @endif
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        Find amazing events happening near you
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('organizer.events.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
    <div class="modal-bg max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="modal-header rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-white mb-2">Search Events</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by event name, location..."
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md" style="color: black;">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-white mb-2">Category</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" style="color: black;">
                        <option value="">All Categories</option>
                        @if(isset($categories) && $categories->count())
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ isset($category) && $category->id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="block text-sm font-medium text-white mb-2">Location</label>
                    <input type="text" name="location" id="location" placeholder="City, Country..."
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md" style="color: black;">
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium tex-white mb-2">From Date</label>
                    <input type="date" name="date_from" id="date_from"
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md" style="color: black;">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-white mb-2">To Date</label>
                    <input type="date" name="date_to" id="date_to"
                        class="focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 text-sm border-gray-300 rounded-md" style="color: black;">
                </div>
                <div class="flex items-end">
                    <button type="button" id="applyFilters" class="w-full bg-purple-600 text-white px-4 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-white">
                    @if(isset($category))
                    {{ $category->name }} Events
                    @else
                    All Events
                    @endif
                    <span class="text-lg text-purple-400">({{ $events->total() }} found)</span>
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-300">Sort by:</span>
                    <select id="sort" name="sort" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" style="color: black;">
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
                        @if($event->banner_image)
                            <img src="{{ Storage::disk('public')->url($event->banner_image) }}"
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        @if($event->is_featured)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Featured
                            </span>
                        </div>
                        @endif

                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $event->category->name ?? 'Uncategorized' }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute bottom-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $event->status == 'approved' ? 'bg-green-100 text-green-800' :
                                ($event->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' :
                                ($event->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Event Content -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1 flex-1 mr-2">
                                <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                    {{ $event->title }}
                                </a>
                            </h3>

                            {{-- Show edit and status controls only if user owns the event or is admin --}}
                            @if(auth()->check() && (auth()->id() === $event->organizer_id || auth()->user()->role === 'admin'))
                                <div class="flex flex-col items-end space-y-2">

                                    {{-- Edit Event --}}
                                    <a href="{{ route('organizer.events.edit', $event) }}"
                                    class="inline-flex items-center p-2 text-sm text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200"
                                    title="Edit Event">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                                                    a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    {{-- Delete Event --}}
                                    @if($event->organizer_id == Auth::id() || Auth::user()->role === 'admin')
                                        <!-- Delete Button -->
                                        <button type="button"
                                                onclick="openDeleteModal('{{ $event->id }}', '{{ $event->title }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                                    01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1
                                                    1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>

                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $event->id }}"
                                            action="{{ route('organizer.events.destroy', $event) }}"
                                            method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif

                                    <!-- Delete Confirmation Modal -->
                                    <div id="deleteModal"
                                        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">

                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl transform transition-all duration-300 scale-95 hover:scale-100">
                                            <div class="p-6 sm:p-8">
                                                <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 mb-3 sm:mb-4 text-center">
                                                    Confirm Deletion
                                                </h2>

                                                <p class="text-gray-700 text-sm sm:text-base mb-6 text-center leading-relaxed" id="deleteModalMessage">
                                                    Are you sure you want to delete this event?
                                                </p>

                                                <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                                                    <button type="button"
                                                            onclick="closeDeleteModal()"
                                                            class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="button"
                                                            id="confirmDeleteButton"
                                                            class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base bg-red-600 text-white rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                                        Yes, Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Event Status Actions --}}
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        {{-- Submit for Approval --}}
                                        @if($event->status === 'draft')
                                            <x-event-status-button
                                                :event="$event"
                                                color="yellow"
                                                nextStatus="pending_approval"
                                                label="Submit for Approval"
                                            />
                                        @endif

                                        {{-- Approve Event (Admin Only) --}}
                                        @if($event->status === 'pending_approval' && auth()->user()->role === 'admin')
                                            <x-event-status-button
                                                :event="$event"
                                                color="green"
                                                nextStatus="approved"
                                                label="Approve"
                                            />
                                        @endif

                                        {{-- Cancel Event --}}
                                        @if(in_array($event->status, ['draft', 'pending_approval', 'approved']))
                                            <x-event-status-button
                                                :event="$event"
                                                color="red"
                                                nextStatus="cancelled"
                                                label="Cancel"
                                                confirm="Are you sure you want to cancel this event?"
                                            />
                                        @endif

                                        {{-- Restore to Draft --}}
                                        @if($event->status === 'cancelled')
                                            <x-event-status-button
                                                :event="$event"
                                                color="gray"
                                                nextStatus="draft"
                                                label="Restore to Draft"
                                            />
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>


                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ Str::limit($event->description, 100) }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $event->start_date ? $event->start_date->format('M j, Y g:i A') : 'Date TBD' }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $event->location ?? 'Location TBD' }}
                            </div>
                        </div>

                        <!-- Price and Actions -->
                        <div class="flex items-center justify-between">
                            <div>
                                @if($event->ticket_price > 0)
                                <span class="text-lg font-bold text-gray-900">KES {{ number_format($event->ticket_price, 2) }}</span>
                                @else
                                <span class="text-lg font-bold text-green-600">Free</span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <!-- Quick Actions for Organizer -->
                                @if(auth()->check() && auth()->id() == $event->organizer_id)
                                <a href="{{ route('organizer.events.edit', $event) }}"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                @endif

                                <a href="{{ route('events.show', $event) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Details
                                </a>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-xs text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $event->capacity ?? 'Unlimited' }} capacity
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ $event->tickets()->sum('quantity_sold') ?? 0 }} sold
                            </span>
                        </div>

                        <!-- Organizer Info -->
                        @if($event->organizer)
                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center text-xs text-gray-500">
                            <span>By {{ $event->organizer->name }}</span>
                            @if($event->organizer->company_name)
                            <span class="mx-1">•</span>
                            <span>{{ $event->organizer->company_name }}</span>
                            @endif
                        </div>
                        @endif
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
                <p class="mt-2 text-sm text-white">
                    @if(isset($category))
                    There are no {{ $category->name }} events matching your criteria.
                    @else
                    There are no events matching your criteria. Try adjusting your filters.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('organizer.events') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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

<script>
    let currentEventId = null;

    function openDeleteModal(eventId, eventTitle) {
        currentEventId = eventId;
        const message = `Are you sure you want to permanently delete "${eventTitle}"? This action cannot be undone.`;
        document.getElementById('deleteModalMessage').textContent = message;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        currentEventId = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        if (currentEventId) {
            const form = document.getElementById(`delete-form-${currentEventId}`);
            if (form) form.submit();
        }
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
