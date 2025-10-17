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
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        main { min-height: calc(100vh - 200px); }
    </style>
</head>
<body x-data="{ signupOpen: false }" class="font-sans antialiased bg-gray-50">

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
                        <x-nav-link href="{{ route('organizers.index') }}" :active="request()->routeIs('organizers.*')">For Organizers</x-nav-link>

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
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                            <button @click="signupOpen = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Sign up
                            </button>
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

                <div class="flex justify-end">
                    <x-button class="ml-2">Register</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Page Content -->
    <main class="pt-20">
        @yield('content')
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
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('help') }}" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Organizer Guides</a></li>
                        <li><a href="#" class="hover:text-white">API Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Facebook</span> ...SVG...</a>
                        <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Twitter</span> ...SVG...</a>
                        <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Instagram</span> ...SVG...</a>
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
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
