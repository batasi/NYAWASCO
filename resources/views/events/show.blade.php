@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        @if($event->banner_image)
        <div class="h-64 md:h-96 bg-cover bg-center" style="background-image: url('{{ $event->banner_image }}')"></div>
        @endif

        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-6">
                <div class="flex-1">
                    <!-- Event Status Badges -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if(!$event->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif

                        @if($event->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Featured
                            </span>
                        @endif

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($event->status) }}
                        </span>

                        @if($event->category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $event->category->name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                    <!-- Organizer Info -->
                    @if($event->organizer)
                    <div class="flex items-center mb-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Organized by <strong>{{ $event->organizer->name }}</strong></span>
                        </div>
                    </div>
                    @endif

                    <!-- Event Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Date -->
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold">{{ $event->start_date->format('M j, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $event->start_date->format('g:i A') }}</div>
                            </div>
                        </div>

                        <!-- Location -->
                        @if($event->location)
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold">Location</div>
                                <div class="text-sm text-gray-500">{{ $event->location }}</div>
                            </div>
                        </div>
                        @endif

                        <!-- Ticket Price -->
                        @if($event->ticket_price)
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <div>
                                <div class="font-semibold">From</div>
                                <div class="text-sm text-gray-500">KES {{ number_format($event->ticket_price, 2) }}</div>
                            </div>
                        </div>
                        @endif

                        <!-- Capacity -->
                        @if($event->capacity)
                        <div class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold">Capacity</div>
                                <div class="text-sm text-gray-500">{{ number_format($event->capacity) }} people</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 mt-4 md:mt-0">
                    @if($event->is_active && $event->status === 'approved')
                        <a href="{{ route('events.tickets', $event) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition duration-200">
                            Get Tickets
                        </a>
                    @endif

                    <button class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200">
                        Share Event
                    </button>

                    @can('edit', $event)
                        <a href="{{ route('events.edit', $event) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition duration-200">
                            Edit Event
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Event</h2>
                <div class="prose max-w-none text-gray-700">
                    @if($event->description)
                        {!! nl2br(e($event->description)) !!}
                    @else
                        <p class="text-gray-500 italic">No description provided for this event.</p>
                    @endif
                </div>
            </div>

            <!-- Tickets Section -->
            @if($event->tickets->count() > 0)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Tickets</h2>
                <div class="space-y-4">
                    @foreach($event->tickets as $ticket)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $ticket->name }}</h3>
                                @if($ticket->description)
                                    <p class="text-gray-600 mb-3">{{ $ticket->description }}</p>
                                @endif
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <span>KES {{ number_format($ticket->price, 2) }}</span>
                                    @if($ticket->quantity_available)
                                        <span>{{ $ticket->quantity_available - $ticket->quantity_sold }} available</span>
                                    @endif
                                    <span>Max {{ $ticket->max_per_order }} per order</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                @if($ticket->is_active && $ticket->sale_start_date <= now() && $ticket->sale_end_date >= now())
                                    <a href="{{ route('events.tickets', $event) }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition duration-200">
                                        Select
                                    </a>
                                @else
                                    <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg font-semibold text-sm">
                                        Not Available
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Organizer Info Card -->
            @if($event->organizer)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Organizer</h3>
                <div class="flex items-center space-x-4">
                    @if($event->organizer->avatar)
                        <img src="{{ $event->organizer->avatar }}" alt="{{ $event->organizer->name }}"
                             class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold text-lg">
                                {{ substr($event->organizer->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $event->organizer->name }}</h4>
                        @if($event->organizer->company_name)
                            <p class="text-sm text-gray-600">{{ $event->organizer->company_name }}</p>
                        @endif
                    </div>
                </div>
                @if($event->organizer->bio)
                    <p class="text-sm text-gray-600 mt-3 line-clamp-3">{{ $event->organizer->bio }}</p>
                @endif

                @if($event->organizer->total_events > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Events Organized</span>
                            <span class="font-semibold">{{ $event->organizer->total_events }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 mt-1">
                            <span>Total Attendees</span>
                            <span class="font-semibold">{{ number_format($event->organizer->total_attendees) }}</span>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Event Stats -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status</span>
                        <span class="font-semibold capitalize">{{ $event->status }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Active</span>
                        <span class="font-semibold {{ $event->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $event->is_active ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Featured</span>
                        <span class="font-semibold {{ $event->is_featured ? 'text-yellow-600' : 'text-gray-600' }}">
                            {{ $event->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Created</span>
                        <span class="font-semibold">{{ $event->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Share Event -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Share This Event</h3>
                <div class="flex space-x-3">
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                        Facebook
                    </button>
                    <button class="flex-1 bg-blue-400 hover:bg-blue-500 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                        Twitter
                    </button>
                    <button class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                        WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add some custom styles -->
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
