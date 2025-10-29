@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Banner Section -->
    <div class="relative bg-gray-900 h-64 md:h-80 lg:h-96">
        @if($event->banner_image)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->title }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
        </div>
        @else
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-700"></div>
        @endif
    </div>

    <!-- Content Section -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20">
        <!-- Status Badges -->
        <div class="flex flex-wrap gap-3 mb-6 mt-6">
            @if(!$event->is_active)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-500/20 text-red-300 border border-red-500/30 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                    Inactive
                </span>
            @endif

            @if($event->is_featured)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Featured
                </span>
            @endif

            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 backdrop-blur-sm capitalize">
                {{ $event->status }}
            </span>

            @if($event->category)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-500/20 text-green-300 border border-green-500/30 backdrop-blur-sm">
                    {{ $event->category->name }}
                </span>
            @endif

            @if($event->voting_contest_id)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-purple-500/20 text-purple-300 border border-purple-500/30 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Voting Contest
                </span>
            @endif
        </div>

        <!-- Event Title -->
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
            {{ $event->title }}
        </h1>

        <!-- Organizer Info -->
        @if($event->organizer)
        <div class="flex items-center mb-8">
            @if($event->organizer->avatar)
                <img src="{{ $event->organizer->avatar }}" alt="{{ $event->organizer->name }}"
                     class="w-10 h-10 rounded-full border-2 border-white/20 mr-3">
            @else
                <div class="w-10 h-10 bg-blue-500/20 rounded-full border-2 border-white/20 flex items-center justify-center mr-3">
                    <span class="text-blue-300 font-semibold text-sm">
                        {{ substr($event->organizer->name, 0, 1) }}
                    </span>
                </div>
            @endif
            <div class="text-white/80">
                <span class="font-medium">Organized by</span>
                <span class="font-semibold text-white ml-1">{{ $event->organizer->name }}</span>
                @if($event->organizer->company_name)
                    <span class="text-white/60 ml-2">• {{ $event->organizer->company_name }}</span>
                @endif
            </div>
        </div>
        @endif

        <!-- Event Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Date & Time -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center text-white/80 mb-2">
                    <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold">Date & Time</span>
                </div>
                <div class="text-white">
                    <div class="font-bold text-lg">{{ $event->start_date->format('F j, Y') }}</div>
                    <div class="text-white/80">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</div>
                </div>
            </div>

            <!-- Location -->
            @if($event->location)
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center text-white/80 mb-2">
                    <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-semibold">Location</span>
                </div>
                <div class="text-white font-bold text-lg">{{ $event->location }}</div>
            </div>
            @endif

            <!-- Ticket Price -->
            @if($event->tickets->min('price') > 0)
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center text-white/80 mb-2">
                    <svg class="w-5 h-5 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <span class="font-semibold">Starting From</span>
                </div>
                <div class="text-white font-bold text-lg">KES {{ number_format($event->tickets->min('price'), 2) }}</div>
            </div>
            @endif

            <!-- Capacity -->
            @if($event->capacity)
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center text-white/80 mb-2">
                    <svg class="w-5 h-5 mr-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-semibold">Capacity</span>
                </div>
                <div class="text-white font-bold text-lg">{{ number_format($event->capacity) }}</div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-12">
            @if($event->status === 'approved' && $event->is_active && $event->start_date > now())
                {{-- Main "Get Tickets" button - goes to general purchase page --}}
                <a href="{{ route('tickets.purchase.show', $event) }}"
                class="group bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl inline-flex items-center justify-center">
                    <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Get Tickets
                </a>
            @endif

            @if($event->voting_contest_id)
            <a href="{{ route('voting.show', $event->voting_contest_id) }}"
               class="group bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl inline-flex items-center justify-center">
                <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Vote Now
            </a>
            @endif

            <div class="flex gap-3">
                @can('edit', $event)
                <a href="{{ route('events.edit', $event) }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 backdrop-blur-sm border border-white/20 hover:border-white/30 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Event
                </a>
                @endcan

                <button onclick="shareEvent()"
                        class="bg-white/20 hover:bg-white/30 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 backdrop-blur-sm border border-white/20 hover:border-white/30 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Share
                </button>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description Section -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-1.5 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold text-gray-900">About This Event</h2>
                    </div>
                    <div class="prose prose-lg max-w-none text-gray-700">
                        @if($event->description)
                            {!! nl2br(e($event->description)) !!}
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 text-lg">No description provided for this event.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tickets Section -->
                @if($event->tickets->count() > 0)
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center">
                            <div class="w-1.5 h-8 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full mr-4"></div>
                            <h2 class="text-2xl font-bold text-gray-900">Available Tickets</h2>
                        </div>
                        <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $event->tickets->count() }} type(s)
                        </span>
                    </div>

                    <div class="space-y-6">
                        @foreach($event->tickets as $ticket)
                        <div class="group border-2 border-gray-100 hover:border-green-300 rounded-xl p-6 transition-all duration-300 hover:shadow-lg">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $ticket->name }}</h3>
                                        <div class="text-2xl font-bold text-green-600">
                                            KES {{ number_format($ticket->price, 2) }}
                                        </div>
                                    </div>

                                    @if($ticket->description)
                                        <p class="text-gray-700 mb-4 leading-relaxed">{{ $ticket->description }}</p>
                                    @endif

                                    <div class="flex flex-wrap gap-6 text-sm">
                                        @if($ticket->quantity_available)
                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $ticket->quantity_available - $ticket->quantity_sold }} available
                                        </div>
                                        @endif

                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Max {{ $ticket->max_per_order }} per order
                                        </div>

                                        @if($ticket->sale_start_date && $ticket->sale_end_date)
                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Sale: {{ $ticket->sale_start_date->format('M j, g:i A') }} - {{ $ticket->sale_end_date->format('M j, g:i A') }}
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Countdown Timer for Sale Start -->
                                    @if($ticket->sale_start_date && $ticket->sale_start_date > now())
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center text-blue-800">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="font-semibold">Sale starts in:</span>
                                            <span class="ml-2 font-bold" id="countdown-{{ $ticket->id }}"></span>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="lg:text-right">
                                    @php
                                        // Improved availability check with timezone consideration
                                        $isActive = $ticket->is_active;

                                        // For sale start: use start of day to ignore time differences
                                        $saleStarted = !$ticket->sale_start_date || $ticket->sale_start_date <= now();

                                        // For sale end: use end of day to give full day
                                        $saleNotEnded = !$ticket->sale_end_date || $ticket->sale_end_date->endOfDay() >= now();

                                        $hasQuantity = !$ticket->quantity_available || ($ticket->quantity_available - $ticket->quantity_sold) > 0;

                                        $isAvailable = $isActive && $saleStarted && $saleNotEnded && $hasQuantity;
                                    @endphp

                                    @if($isAvailable)
                                        {{-- For individual ticket selection - goes to purchase page with ticket pre-selected --}}
                                        <a href="{{ route('tickets.purchase.show', $event) }}?ticket_id={{ $ticket->id }}"
                                        class="group bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg inline-flex items-center justify-center">
                                            Select Ticket
                                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    @else
                                        @if($ticket->sale_start_date && $ticket->sale_start_date > now())
                                            <div class="bg-blue-100 text-blue-800 px-6 py-3 rounded-xl font-semibold inline-flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Coming Soon
                                            </div>
                                        @elseif($ticket->sale_end_date && $ticket->sale_end_date < now())
                                            <div class="bg-red-100 text-red-800 px-6 py-3 rounded-xl font-semibold inline-flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Sale Ended
                                            </div>
                                        @else
                                            <div class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold inline-flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                Not Available
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif


                <!-- Voting Contest Section -->
                @if($event->votingContest)
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-1.5 h-8 bg-white rounded-full mr-4"></div>
                        <h2 class="text-2xl font-bold">Voting Contest</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h3 class="text-xl font-bold mb-3">{{ $event->votingContest->title }}</h3>
                            <p class="text-purple-100 mb-4 leading-relaxed">
                                {{ $event->votingContest->description ?: 'Participate in our exciting voting contest!' }}
                            </p>
                            <div class="space-y-2 text-purple-100">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    KES {{ number_format($event->votingContest->price_per_vote, 2) }} per vote
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Max {{ $event->votingContest->max_votes_per_user }} votes per user
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('voting.show', $event->votingContest->id) }}"
                               class="bg-white text-purple-600 hover:bg-purple-50 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center mx-auto">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                View Contest
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Organizer Card -->
                @if($event->organizer)
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Organizer
                    </h3>

                    <div class="flex items-center space-x-4 mb-4">
                        @if($event->organizer->avatar)
                            <img src="{{ $event->organizer->avatar }}" alt="{{ $event->organizer->name }}"
                                 class="w-14 h-14 rounded-xl object-cover border-2 border-gray-100">
                        @else
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center border-2 border-gray-100">
                                <span class="text-white font-bold text-lg">
                                    {{ substr($event->organizer->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $event->organizer->name }}</h4>
                            @if($event->organizer->company_name)
                                <p class="text-sm text-gray-600">{{ $event->organizer->company_name }}</p>
                            @endif
                        </div>
                    </div>

                    @if($event->organizer->bio)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $event->organizer->bio }}</p>
                    @endif

                    @if($event->organizer->total_events > 0)
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Events Organized</span>
                                <span class="font-bold text-gray-900">{{ $event->organizer->total_events }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Attendees</span>
                                <span class="font-bold text-gray-900">{{ number_format($event->organizer->total_attendees) }}</span>
                            </div>
                            @if($event->organizer->rating > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Rating</span>
                                <div class="flex items-center">
                                    <span class="font-bold text-gray-900 mr-1">{{ $event->organizer->rating }}/5</span>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
                @endif

                <!-- Event Stats Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Event Stats
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Status</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 capitalize">
                                {{ $event->status }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Active</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $event->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $event->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Featured</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $event->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $event->is_featured ? 'Featured' : 'Regular' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Created</span>
                            <span class="text-sm font-bold text-gray-900">{{ $event->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Share Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share Event
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="shareOnFacebook()" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </button>

                        <button onclick="shareOnTwitter()" class="flex items-center justify-center gap-2 bg-blue-400 hover:bg-blue-500 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            Twitter
                        </button>

                        <button onclick="shareOnWhatsApp()" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893c0-3.181-1.24-6.166-3.48-8.407"/>
                            </svg>
                            WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Force all text to be dark and visible on white background */
    .bg-white * {
        color: #1f2937 !important; /* gray-800 */
    }

    /* Specific overrides for colored elements */
    .bg-white .text-green-600,
    .bg-white .text-green-700 {
        color: #059669 !important; /* emerald-600 */
    }

    .bg-white .text-blue-600,
    .bg-white .text-blue-700 {
        color: #2563eb !important; /* blue-600 */
    }

    .bg-white .text-orange-600,
    .bg-white .text-orange-700 {
        color: #ea580c !important; /* orange-600 */
    }

    .bg-white .text-purple-600,
    .bg-white .text-purple-700 {
        color: #7c3aed !important; /* purple-600 */
    }

    /* Status buttons - keep their colors but ensure visibility */
    .bg-white .bg-blue-100 {
        background-color: #dbeafe !important;
        color: #1e40af !important; /* blue-800 */
    }

    .bg-white .bg-red-100 {
        background-color: #fee2e2 !important;
        color: #991b1b !important; /* red-800 */
    }

    .bg-white .bg-gray-100 {
        background-color: #f3f4f6 !important;
        color: #374151 !important; /* gray-700 */
    }

    /* Countdown timer */
    .bg-white .bg-blue-50 {
        background-color: #eff6ff !important;
        color: #1e40af !important; /* blue-800 */
    }

    /* Select Ticket button - keep white text on gradient */
    .bg-gradient-to-r.from-green-500.to-emerald-600 {
        color: white !important;
    }

    /* Headers and important text */
    .bg-white h1,
    .bg-white h2,
    .bg-white h3,
    .bg-white .font-bold,
    .bg-white .font-semibold {
        color: #111827 !important; /* gray-900 */
    }

    /* Icons - ensure they're visible */
    .bg-white svg {
        color: inherit !important;
    }

    /* Badge text */
    .bg-white .text-gray-500 {
        color: #6b7280 !important; /* gray-500 */
    }

    /* Override any light text that might be hard to read */
    .bg-white .text-gray-400,
    .bg-white .text-gray-300,
    .bg-white .text-gray-200 {
        color: #4b5563 !important; /* gray-600 */
    }
</style>
<script>
    function shareEvent() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $event->title }}',
                text: 'Check out this event!',
                url: window.location.href,
            })
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing:', error));
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Event link copied to clipboard!');
            });
        }
    }

    function shareOnFacebook() {
        const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`;
        window.open(url, '_blank', 'width=600,height=400');
    }

    function shareOnTwitter() {
        const text = `Check out this event: {{ $event->title }}`;
        const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(window.location.href)}`;
        window.open(url, '_blank', 'width=600,height=400');
    }

    function shareOnWhatsApp() {
        const text = `Check out this event: {{ $event->title }} - ${window.location.href}`;
        const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank', 'width=600,height=400');
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($event->tickets as $ticket)
            @if($ticket->sale_start_date && $ticket->sale_start_date > now())
                // Set the date we're counting down to
                var countDownDate{{ $ticket->id }} = new Date("{{ $ticket->sale_start_date->toISOString() }}").getTime();

                // Update the count down every 1 second
                var x{{ $ticket->id }} = setInterval(function() {
                    // Get today's date and time
                    var now = new Date().getTime();

                    // Find the distance between now and the count down date
                    var distance = countDownDate{{ $ticket->id }} - now;

                    // Time calculations for days, hours, minutes and seconds
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Display the result in the element with id="countdown-{{ $ticket->id }}"
                    document.getElementById("countdown-{{ $ticket->id }}").innerHTML =
                        (days > 0 ? days + "d " : "") +
                        hours.toString().padStart(2, '0') + "h " +
                        minutes.toString().padStart(2, '0') + "m " +
                        seconds.toString().padStart(2, '0') + "s";

                    // If the count down is finished, write some text and reload
                    if (distance < 0) {
                        clearInterval(x{{ $ticket->id }});
                        document.getElementById("countdown-{{ $ticket->id }}").innerHTML = "Sale Started!";
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }, 1000);
            @endif
        @endforeach
    });
</script>
@endsection
