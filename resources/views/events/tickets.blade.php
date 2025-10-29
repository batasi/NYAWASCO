@extends('layouts.app')

@section('title', "Tickets")

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tickets for {{ $event->title }}</h1>

        @if($event->tickets->count() > 0)
            <div class="space-y-6">
                @foreach($event->tickets as $ticket)
                <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $ticket->name }}</h3>
                            @if($ticket->description)
                                <p class="text-gray-600 mb-4">{{ $ticket->description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-6 text-sm text-gray-500 mb-4">
                                <span class="font-semibold text-lg text-green-600">
                                    KES {{ number_format($ticket->price, 2) }}
                                </span>
                                @if($ticket->quantity_available)
                                    <span>{{ $ticket->quantity_available - $ticket->quantity_sold }} tickets left</span>
                                @endif
                                <span>Max {{ $ticket->max_per_order }} per order</span>
                            </div>
                        </div>
                        <div class="ml-6">
                            @if($ticket->is_active && $ticket->sale_start_date <= now() && $ticket->sale_end_date >= now())
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                                    Buy Ticket
                                </button>
                            @else
                                <span class="bg-gray-300 text-gray-600 px-6 py-3 rounded-lg font-semibold">
                                    Not Available
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Tickets Available</h3>
                <p class="text-gray-600">There are no tickets available for this event at the moment.</p>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('events.show', $event) }}"
               class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Back to Event Details
            </a>
        </div>
    </div>
</div>
@endsection
