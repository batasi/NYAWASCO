@extends('layouts.app')

@section('title', 'Organizer Dashboard - Javent')

@section('content')
@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;
@endphp

<div class="min-h-screen bg-gradient-to-br from-gray-900 to-purple-900">
    <!-- Header -->
    <div class="bg-black/50 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                       @auth
                            Welcome back, {{ auth()->user()->name }}!
                        @else
                            Welcome, Guest!
                        @endauth
                    </h1>
                    <p class="mt-2 text-lg text-gray-300">
                        Here's what's happening with your events today.
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="{{ route('organizer.events.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Event
                    </a>
                    <a href="{{ route('organizer.voting.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-600 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Create Voting
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Events -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-xl bg-purple-500/20">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Total Events</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_events'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['active_events'] ?? 0 }} active</p>
                    </div>
                </div>
            </div>

            <!-- Voting Contests -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-xl bg-blue-500/20">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Voting Contests</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_voting_contests'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['active_contests'] ?? 0 }} ongoing</p>
                    </div>
                </div>
            </div>

            <!-- Ticket Sales -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-xl bg-green-500/20">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Ticket Sales</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_ticket_sales'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['today_sales'] ?? 0 }} today</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 hover:transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-xl bg-yellow-500/20">
                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Total Revenue</p>
                        <p class="text-2xl font-bold text-white">KSh {{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                        <p class="text-xs text-gray-400 mt-1">KSh {{ number_format($stats['month_revenue'] ?? 0, 0) }} this month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Recent Activity -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Ticket Sales -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
                    <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Recent Ticket Sales</h3>
                            <p class="text-sm text-gray-300 mt-1">Latest purchases from your events</p>
                        </div>
                        @if(Route::has('organizer.ticket-sales'))
                            <a href="{{ route('organizer.ticket-sales') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600/50 hover:bg-purple-600 rounded-lg transition-colors duration-200">
                                View All
                            </a>
                        @endif
                    </div>

                    <div class="divide-y divide-white/10">
                        @if($recentTicketSales->count() > 0)
                            @foreach($recentTicketSales as $sale)
                            <div class="p-6 hover:bg-white/5 transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($sale->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-white truncate">{{ $sale->event->title ?? 'Event' }}</h4>
                                                <p class="text-sm text-gray-300 mt-1">{{ $sale->user->name ?? 'Customer' }}</p>
                                                <div class="flex items-center mt-2 space-x-4 text-xs text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                        </svg>
                                                        KSh {{ number_format($sale->final_amount ?? 0, 2) }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                        </svg>
                                                        {{ $sale->quantity ?? 1 }} {{ Str::plural('ticket', $sale->quantity ?? 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                            Paid
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $sale->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto bg-gray-600/20 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-300">No ticket sales yet</h3>
                                <p class="mt-2 text-sm text-gray-400">Ticket sales will appear here once customers purchase tickets to your events.</p>
                                <div class="mt-6">
                                    <a href="{{ route('organizer.events.create') }}"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-lg transition-all duration-200">
                                        Create Your First Event
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
                    <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Upcoming Events</h3>
                            <p class="text-sm text-gray-300 mt-1">Your events happening soon</p>
                        </div>
                        <a href="{{ route('organizer.events') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600/50 hover:bg-blue-600 rounded-lg transition-colors duration-200">
                            View All
                        </a>
                    </div>

                    <div class="divide-y divide-white/10">
                        @if($upcomingEvents->count() > 0)
                            @foreach($upcomingEvents as $event)
                            <div class="p-6 hover:bg-white/5 transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-semibold text-white truncate">{{ $event->title }}</h4>
                                        <div class="flex items-center mt-2 space-x-4 text-sm text-gray-300">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $event->start_date->format('M j, Y g:i A') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $event->location }}
                                            </span>
                                        </div>
                                        @if($event->ticket_price > 0)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                KSh {{ number_format($event->ticket_price, 2) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $event->status === 'approved' ? 'bg-green-500/20 text-green-400 border border-green-500/30' :
                                               ($event->status === 'pending_approval' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' :
                                               'bg-gray-500/20 text-gray-400 border border-gray-500/30') }}">
                                            {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                        </span>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('organizer.events.edit', $event) }}"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-400 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg transition-colors duration-200">
                                                Edit
                                            </a>
                                            <a href="{{ route('events.show', $event) }}"
                                                class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-300 bg-gray-500/10 hover:bg-gray-500/20 rounded-lg transition-colors duration-200">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto bg-gray-600/20 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-300">No upcoming events</h3>
                                <p class="mt-2 text-sm text-gray-400">Start by creating your first event.</p>
                                <div class="mt-6">
                                    <a href="{{ route('organizer.events.create') }}"
                                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-lg transition-all duration-200">
                                        Create Your First Event
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions & Analytics -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('organizer.events.create') }}"
                            class="flex items-center p-3 bg-purple-600/20 hover:bg-purple-600/30 rounded-lg transition-colors duration-200 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">Create New Event</p>
                                <p class="text-xs text-gray-300">Set up a new event</p>
                            </div>
                        </a>
                        <a href="{{ route('organizer.voting.create') }}"
                            class="flex items-center p-3 bg-blue-600/20 hover:bg-blue-600/30 rounded-lg transition-colors duration-200 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">Start Voting Contest</p>
                                <p class="text-xs text-gray-300">Create a new voting competition</p>
                            </div>
                        </a>
                        <a href="{{ route('organizer.ticket-sales') }}"
                            class="flex items-center p-3 bg-green-600/20 hover:bg-green-600/30 rounded-lg transition-colors duration-200 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">View Ticket Sales</p>
                                <p class="text-xs text-gray-300">See detailed sales reports</p>
                            </div>
                        </a>
                        <a href="{{ route('organizer.bookings') }}"
                            class="flex items-center p-3 bg-yellow-600/20 hover:bg-yellow-600/30 rounded-lg transition-colors duration-200 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">Manage Bookings</p>
                                <p class="text-xs text-gray-300">View and manage event bookings</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Performance</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Event Attendance Rate</span>
                                <span>{{ $stats['attendance_rate'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-600/20 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['attendance_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Ticket Conversion</span>
                                <span>{{ $stats['conversion_rate'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-600/20 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['conversion_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Customer Satisfaction</span>
                                <span>{{ $stats['satisfaction_rate'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-600/20 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $stats['satisfaction_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Notifications -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
                    <div class="px-6 py-4 border-b border-white/10">
                        <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @if($recentActivity->count() > 0)
                            @foreach($recentActivity as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-white/5 rounded-lg">
                                <div class="flex-shrink-0 w-8 h-8 bg-{{ $activity->type_color }}-500/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-{{ $activity->type_color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity->icon }}"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-white">{{ $activity->message }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-400">No recent activity</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
