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

           <!-- Hero Slideshow Section -->
            <section class="mb-16 -mx-4 sm:-mx-6 lg:-mx-8 mt-2">
                <div wire:ignore class="swiper hero-swiper h-80 md:h-[450px] lg:h-[550px] w-screen">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                                alt="Live Events"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Discover Amazing Events</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Find and book tickets for the hottest events in your city</p>
                                    <a href="{{ route('events.index') }}"
                                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                        Explore Events
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="/img/javent.png"
                                alt="Create Events"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Host Your Event</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Reach thousands of attendees with our powerful platform</p>
                                    <button @click="signupOpen = true" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105">
                                        Get Started
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                                alt="Voting Contests"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Join Live Voting</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Participate in exciting contests and shape the outcomes</p>
                                    <a href="{{ route('voting.index') }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                        Vote Now
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="swiper-slide relative">

                            <div class="absolute inset-0 bg-cover bg-center scale-110 blur-sm"
                                style="background-image: url('/img/dance.jpeg')"></div>
                            <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
                            <img src="/img/dance.jpeg"
                                alt="Book Tickets"
                                class="w-full h-full object-contain opacity-50 z-20 relative">
                            <div class="absolute inset-0 z-30 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 text-white drop-shadow-2xl">Book Your Tickets</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 text-white font-medium drop-shadow-lg">Secure your spot at amazing events with easy, fast booking</p>
                                    <a href="{{ route('events.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white px-8 py-3 md:px-10 md:py-4 rounded-full font-bold text-lg md:text-xl transition duration-300 transform hover:scale-105 inline-block shadow-2xl">
                                        Book Your Tickets
                                    </a>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination !bottom-4"></div>

                   <!-- Navigation Buttons - Properly Styled -->
                    <div class="swiper-button-prev !w-4!h-4 md:!w-12 md:!h-12 !left-1 md:!left-2 bg-white bg-opacity-10 rounded-full shadow-lg hover:bg-purple hover:scale-90 transition-all duration-300"></div>
                    <div class="swiper-button-next !w-4 !h-4 md:!w-12 md:!h-12 !right-1 md:!right-2 bg-white bg-opacity-10 rounded-full shadow-lg hover:bg-purple hover:scale-90 transition-all duration-300"></div>
                </div>
            </section>

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
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-12">How Javent Works</h2>
            <div class="grid md:grid-cols-2 gap-4">
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
                <div class="bg-black rounded-2xl shadow-lg p-8 vote-gradient text-white">
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
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-12">What Our Community Says</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            AJ
                        </div>
                        <div>
                            <h4 class="font-semibold text-black">Keffa.</h4>
                            <p class="text-gray-600 text-sm">CEO, HeartBeat of Style</p>
                        </div>
                    </div>
                    <p class="text-gray-700">“Javent made our event feel truly interactive through ticket bookings and more. The live voting kept our audience energized, and the sleek poster designs gave our brand a polished, professional edge.”</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-pink-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            MK
                        </div>
                        <div>
                            <h4 class="font-semibold text-black">Mark K.</h4>
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
                            <h4 class="font-semibold text-black">Sarah M.</h4>
                            <p class="text-gray-600 text-sm">Music Festival Director</p>
                        </div>
                    </div>
                    <p class="text-gray-700">"From ticketing to vendor management, Javent handles everything. A game-changer!"</p>
                </div>
            </div>
        </section>
        <!-- Partners Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-8">Our Trusted Partners</h2>

            <!-- Partner Description -->
            <div class="text-center mb-8">
                <p class="text-gray-300 text-lg max-w-3xl mx-auto leading-relaxed">
                    Collaborating with industry leaders to deliver exceptional event and voting experiences
                    through innovative technology and seamless partnerships.
                </p>
            </div>

            <!-- Partners Logos Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-4xl mx-auto">
                <!-- Partner 1 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35">
                    <div class="w-full h-23 flex items-center justify-center mb-2">
                        <img src="/img/LHTBT.png"
                            alt="HeartBeat of Style"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">HeartBeat of Style</h3>
                </div>

                <!-- Partner 2 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/java.png"
                             alt="JavaPA"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">JavaPA Limited</h3>
                </div>

                <!-- Partner 3 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/GEvents.jpeg"
                             alt="The Giftland Events"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">Giftland Events Limited</h3>
                </div>

                <!-- Partner 4 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/g.png"
                             alt="The Giftland Studios and Restaurant"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">The Giftland Studios and Restaurant</h3>
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

            new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.hero-swiper .swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: '.hero-swiper .swiper-button-next',
                    prevEl: '.hero-swiper .swiper-button-prev',
                },
            });
        });
    </script>

@endsection
