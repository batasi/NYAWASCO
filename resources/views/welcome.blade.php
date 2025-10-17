<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EventSphere</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        .masonry-grid {
            column-count: 3;
            column-gap: 1rem;
        }

        @media (max-width: 768px) {
            .masonry-grid {
                column-count: 1;
            }
        }

        .grid-item {
            break-inside: avoid;
            margin-bottom: 1rem;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .ticket-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .vote-gradient {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
</head>

<body x-data="{ signupOpen: false, loginOpen: false }" class="font-sans antialiased bg-gray-50">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white bg-opacity-95 backdrop-blur-md shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">EventSphere</a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')">Discover Events</x-nav-link>
                        <x-nav-link href="{{ route('voting.index') }}" :active="request()->routeIs('voting.*')">Active Votes</x-nav-link>

                        <!-- For Organizers triggers signup -->
                        <button @click="signupOpen = true" class="text-gray-500 hover:text-gray-700 text-sm font-medium">For Organizers</button>

                        <!-- More Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out"
                                :class="{ 'border-blue-500 text-gray-900': open }">
                                More
                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="origin-top-left absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                 @click.away="open = false">
                                <div class="py-1">
                                    <a href="{{ route('about') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">About Us</a>
                                    <a href="{{ route('contact') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Contact</a>
                                    <a href="{{ route('help') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Help Center</a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('privacy') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Privacy Policy</a>
                                    <a href="{{ route('terms') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Terms of Service</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <!-- Smart Search -->
                    <div class="hidden sm:flex items-center mr-4"><smart-search></smart-search></div>

                    <!-- Authentication Links -->
                    @auth
                        <livewire:navigation.user-dropdown />
                    @else
                        <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                            <button @click="loginOpen = true" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Log in</button>
                            <button @click="signupOpen = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Sign up</button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Sign Up Modal -->
    <div x-show="signupOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button @click="signupOpen = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>

            <h2 class="text-xl font-semibold mb-4">Register</h2>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <x-label for="name" value="Name" />
                    <x-input id="name" type="text" name="name" :value="old('name')" required autofocus class="w-full mt-1" />
                </div>
                <div class="mb-4">
                    <x-label for="email" value="Email" />
                    <x-input id="email" type="email" name="email" :value="old('email')" required class="w-full mt-1" />
                </div>
                <div class="mb-4">
                    <x-label for="password" value="Password" />
                    <x-input id="password" type="password" name="password" required class="w-full mt-1" />
                </div>
                <div class="mb-4">
                    <x-label for="password_confirmation" value="Confirm Password" />
                    <x-input id="password_confirmation" type="password" name="password_confirmation" required class="w-full mt-1" />
                </div>

                <div class="flex justify-between items-center">
                    <x-button class="ml-2">Register</x-button>
                    <span class="text-sm text-gray-600">
                        Already have an account?
                        <button type="button" @click="signupOpen = false; loginOpen = true" class="text-blue-600 hover:underline">Log in</button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-show="loginOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button @click="loginOpen = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>

            <h2 class="text-xl font-semibold mb-4">Log in</h2>

            <x-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>
                <div class="mb-4">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                </div>
                <div class="mb-4 flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <label for="remember_me" class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
                </div>
                <div class="flex justify-between items-center mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <span class="text-sm text-gray-600">
                        Don't have an account?
                        <button type="button" @click="loginOpen = false; signupOpen = true" class="text-blue-600 hover:underline">Sign up</button>
                    </span>
                    <x-button class="ml-4">{{ __('Log in') }}</x-button>
                </div>
            </form>
        </div>
    </div>

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
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Your EventSphere Feed</h2>
                <div class="flex space-x-2">
                    <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500" data-filter="all">
                        All Activities
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500" data-filter="events">
                        Events Only
                    </button>
                    <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500" data-filter="voting">
                        Voting Only
                    </button>
                </div>
            </div>

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
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">How EventSphere Works</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- For Attendees Column -->
                <div class="bg-white rounded-2xl shadow-lg p-8 ticket-gradient text-white">
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
                    <p class="text-gray-700">"Booking tickets through EventSphere is so seamless. The QR check-in made entry a breeze!"</p>
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
                    <p class="text-gray-700">"From ticketing to vendor management, EventSphere handles everything. A game-changer!"</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">EventSphere</h3>
                    <p class="text-gray-400">Your complete universe for events, ticketing, and community engagement.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Organizer Guides</a></li>
                        <li><a href="#" class="hover:text-white">API Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">...</svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">...</svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">...</svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 EventSphere. All rights reserved.</p>
            </div>
        </div>
    </footer>

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
</body>

</html>
