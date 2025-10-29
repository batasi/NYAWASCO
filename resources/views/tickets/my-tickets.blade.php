@extends('layouts.app')

@section('title', 'My Tickets - EventSphere')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        My Tickets
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        Your purchased event tickets
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($ticketPurchases->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($ticketPurchases as $purchase)
            <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-6 hover:border-purple-500 transition-colors duration-200">
                <!-- Event Info -->
                <div class="flex items-start space-x-4 mb-4">
                    @if($purchase->event->banner_image)
                    <img src="{{ Storage::disk('public')->url($purchase->event->banner_image) }}"
                         alt="{{ $purchase->event->title }}"
                         class="w-16 h-16 rounded-lg object-cover">
                    @else
                    <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">{{ $purchase->event->title }}</h3>
                        <p class="text-sm text-gray-400">{{ $purchase->event->start_date->format('M j, Y') }}</p>
                    </div>
                </div>

                <!-- Ticket Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Ticket Type:</span>
                        <span class="text-white">{{ $purchase->ticket->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Quantity:</span>
                        <span class="text-white">{{ $purchase->quantity }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Order Number:</span>
                        <span class="text-white font-mono">{{ $purchase->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Paid:</span>
                        <span class="text-white font-semibold">KES {{ number_format($purchase->final_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $purchase->status === 'paid' ? 'bg-green-100 text-green-800' :
                           ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           'bg-red-100 text-red-800') }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    @if($purchase->status === 'paid')
                    <a href="{{ route('tickets.download', $purchase) }}"
                       class="flex-1 bg-purple-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200 text-center">
                        Download PDF
                    </a>
                    <a href="{{ route('tickets.view', $purchase) }}"
                       class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 text-center">
                        View Online
                    </a>
                    @else
                    <button class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                        Payment Pending
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $ticketPurchases->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-white">No tickets purchased yet</h3>
            <p class="mt-2 text-sm text-gray-400">You haven't purchased any event tickets yet.</p>
            <div class="mt-6">
                <a href="{{ route('events.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Browse Events
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
