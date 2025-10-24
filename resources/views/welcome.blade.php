@extends('layouts.app')

@section('title', $title)

@section('content')


    <!-- Page Content Wrapper -->
    <div class="pt-20">
        {{-- Main content goes here --}}
    </div>

    <!-- Live Pulse Widget -->
    <live-pulse-widget></live-pulse-widget>

    <!-- Hero Section -->
    <homepage-hero
        :user-logged-in="{{ auth()->check() ? 'true' : 'false' }}"
        user-name="{{ auth()->user()->name ?? '' }}"></homepage-hero>
      
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">



        <!-- Intelligent Feed Section -->
        <section class="mb-16">
           

            <!-- Masonry Grid Layout -->
            <div class="masonry-grid">
          
                <!-- Events from Livewire -->
                <livewire:event-feed />
            
                <!-- Voting Contests from Livewire -->
                <livewire:voting-feed />
            </div>
        </section>

        <!-- Dual-Path How It Works Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">How Javent Works</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- For Attendees Column -->
                <div class="bg-black rounded-2xl shadow-lg p-8 ticket-gradient text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">For Attendees & Voters</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">1</span>
                            <div>
                                <h4 class="font-semibold">Discover Amazing Events</h4>
                                <p class="text-white text-opacity-90 text-sm">Browse through curated events and voting contests in your area</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">2</span>
                            <div>
                                <h4 class="font-semibold">Book Tickets or Cast Votes</h4>
                                <p class="text-white text-opacity-90 text-sm">Secure your spot with easy booking or participate in live voting</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">3</span>
                            <div>
                                <h4 class="font-semibold">Enjoy & Engage</h4>
                                <p class="text-white text-opacity-90 text-sm">Attend unforgettable events and see your votes shape outcomes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- For Organizers Column -->
                <div class="bg-white rounded-2xl shadow-lg p-8 vote-gradient text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">For Organizers</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">1</span>
                            <div>
                                <h4 class="font-semibold">Create Your Event</h4>
                                <p class="text-white text-opacity-90 text-sm">Set up events with ticketing, seating, and voting options</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">2</span>
                            <div>
                                <h4 class="font-semibold">Manage & Promote</h4>
                                <p class="text-white text-opacity-90 text-sm">Use our tools to manage attendees and promote your event</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">3</span>
                            <div>
                                <h4 class="font-semibold">Analyze & Grow</h4>
                                <p class="text-white text-opacity-90 text-sm">Get insights from analytics to improve your future events</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">What Our Community Says</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            AJ
                        </div>
                        <div>
                            <h4 class="font-semibold">Aisha J.</h4>
                            <p class="text-gray-600 text-sm">Event Attendee</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"Booking tickets through Javent is so seamless. The QR check-in made entry a breeze!"</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-pink-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            MK
                        </div>
                        <div>
                            <h4 class="font-semibold">Mark K.</h4>
                            <p class="text-gray-600 text-sm">Awards Organizer</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"The voting system transformed our awards night. Real-time results kept everyone engaged!"</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            SM
                        </div>
                        <div>
                            <h4 class="font-semibold">Sarah M.</h4>
                            <p class="text-gray-600 text-sm">Music Festival Director</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"From ticketing to vendor management, Javent handles everything. A game-changer!"</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Script for masonry grid filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const gridItems = document.querySelectorAll('.grid-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');

                    // Filter items
                    gridItems.forEach(item => {
                        if (filter === 'all') {
                            item.style.display = 'block';
                        } else {
                            if (item.classList.contains(filter)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        });
    </script>

@endsection