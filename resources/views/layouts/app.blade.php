<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'EventSphere')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }

        /* Ensure content is not hidden behind fixed navbar */
        main {
            min-height: calc(100vh - 200px); /* Adjust based on your footer height */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white bg-opacity-95 backdrop-blur-md shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">
                            EventSphere
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')">
                            Discover Events
                        </x-nav-link>
                        <x-nav-link href="{{ route('voting.index') }}" :active="request()->routeIs('voting.*')">
                            Active Votes
                        </x-nav-link>
                        <x-nav-link href="{{ route('organizers.index') }}" :active="request()->routeIs('organizers.*')">
                            For Organizers
                        </x-nav-link>

                        <!-- More Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out"
                                :class="{ 'border-blue-500 text-gray-900': open }"
                            >
                                More
                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-left absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                @click.away="open = false"
                            >
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
                    <div class="hidden sm:flex items-center mr-4">
                        <smart-search></smart-search>
                    </div>

                    <!-- Authentication Links -->
                    @auth
                        <livewire:navigation.user-dropdown />
                    @else
                        <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Sign up</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <!-- Add padding-top to push content below the fixed navbar -->
    <main class="pt-20"> <!-- Increased from pt-16 to pt-20 for more spacing -->
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
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987c6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.611-3.223-1.561c-.775-.95-1.172-2.238-1.061-3.537c.111-1.298.718-2.489 1.682-3.253c.964-.764 2.201-1.027 3.423-.741c1.222.286 2.257 1.109 2.822 2.255c.565 1.146.595 2.491.083 3.662c-.512 1.171-1.524 2.038-2.726 2.375v-.001zm7.718 1.151c-.557.395-1.208.637-1.899.703-.69.066-1.392-.046-2.032-.323a3.719 3.719 0 01-1.532-1.129 3.972 3.972 0 01-.782-1.796 4.106 4.106 0 01.121-1.863c.239-.619.637-1.162 1.148-1.565a3.69 3.69 0 011.654-.703c.59-.111 1.198-.064 1.767.137.569.201 1.071.57 1.446 1.065.375.495.607 1.095.67 1.724.063.629-.045 1.262-.312 1.826-.267.565-.682 1.038-1.201 1.369l-.002.001z" clip-rule="evenodd"/>
                            </svg>
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

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
