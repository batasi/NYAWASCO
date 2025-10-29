<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route;
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Javent</title>

       <!-- Favicon -->

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        main {
            min-height: calc(100vh - 200px);
        }

        /* Custom styles for pink, black, and white theme */
        .nav-gradient {
            background: linear-gradient(135deg, #000000 0%, #ec4899 100%);
        }

        .body-gradient {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2d1a2d 100%);
            color: white;
        }

        .modal-bg {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1a2d 100%);
            color: white;
        }

        .footer-bg {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.4);
        }

        .btn-secondary {
            background: #2d1a2d;
            color: white;
            border: 1px solid #ec4899;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #3d2a3d;
            transform: translateY(-2px);
        }

        /* Mobile navigation styles */
        .mobile-menu {
            background: linear-gradient(135deg, #000000 0%, #2d1a2d 100%);
        }

        /* Form styling */
        .form-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Footer grid */
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
        }

        /* Text colors */
        .text-pink {
            color: #ec4899;
        }

        .hover-text-pink:hover {
            color: #ec4899;
        }

        /* Modal header */
        .modal-header {
            background: linear-gradient(135deg, #000000 0%, #ec4899 100%);
            color: white;
        }

        .modal-b {
            background: linear-gradient(135deg, #ff4da6 0%, #e60073 100%);
            color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .modal-b:hover {
            background: linear-gradient(135deg, #ff66b2 0%, #cc0066 100%);
            transform: scale(1.02);
        }

        .modal-h {
            background: linear-gradient(135deg, #000000 0%, #ec4899 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.1);
            letter-spacing: 0.5px;
        }


        .modal-h:hover {
            background: linear-gradient(135deg, #1a1a1a 0%, #f472b6 100%);
            cursor: pointer;
        }



        /* Role selection cards */
        .role-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .role-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ec4899;
        }

        .role-card.peer-checked\:border-pink-500 {
            border-color: #ec4899;
            background: rgba(236, 72, 153, 0.1);
        }

        /* Specific fields backgrounds */
        .specific-fields {
            background: rgba(236, 72, 153, 0.05);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .hero-swiper {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            margin-top: -9.5rem;
        }

        .hero-swiper .swiper-pagination-bullet {
            background: white;
            opacity: 0.6;
            width: 6px;
            height: 6px;
        }

        .hero-swiper .swiper-pagination-bullet-active {
            opacity: 1;
            background: #8b5cf6;
        }

        .hero-swiper .swiper-button-prev,
        .hero-swiper .swiper-button-next {
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .hero-swiper .swiper-button-prev:after,
        .hero-swiper .swiper-button-next:after {
            color: #1f2937 !important; /* Dark gray for better visibility */
            font-weight: 500 !important;
        }

        .hero-swiper .swiper-button-prev:hover,
        .hero-swiper .swiper-button-next:hover {
            background: white !important;
            border-color: white;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .hero-swiper {
                height: 500px !important;
                margin-top: -9.5rem;
            }

            .hero-swiper .swiper-button-prev,
            .hero-swiper .swiper-button-next {
                display: flex !important; /* Ensure arrows are visible */
                width: 36px !important;
                height: 36px !important;
            }

            .hero-swiper .swiper-button-prev:after,
            .hero-swiper .swiper-button-next:after {
                font-size: 16px !important;
            }

            .hero-swiper h2 {
                font-size: 1.75rem !important;
                margin-bottom: 0.5rem !important;
            }

            .hero-swiper p {
                font-size: 1rem !important;
                margin-bottom: 1rem !important;
            }

            .hero-swiper button {
                padding: 0.5rem 1.5rem !important;
                font-size: 0.875rem !important;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .hero-swiper {
                height: 300px !important;
                margin-top: -9.5rem;
            }

            .hero-swiper h2 {
                font-size: 1.5rem !important;
            }

            .hero-swiper p {
                font-size: 0.9rem !important;
            }
        }



    </style>
</head>

<body x-data="{ signupOpen: false, loginOpen: false, mobileMenuOpen: false, intendedUrl: '{{ route('organizers.index') }}',  partnerModalOpen: false,
    currentPartner: {
        name: '',
        image: '',
        description: '',
        socials: null
    },
    openPartnerModal(name, image, description, socials = null) {
        this.currentPartner = {
            name: name,
            image: image,
            description: description,
            socials: socials
        };
        this.partnerModalOpen = true;
    } }" class="font-sans antialiased body-gradient">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 nav-gradient shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo.png') }}"
                            alt="Javent"
                            class="h-20 w-auto"> <!-- Increased to h-20 -->
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">

                    <!-- Core Links -->
                    <x-nav-link href="{{ route('voting.index') }}"
                                :active="request()->routeIs('voting.*')"
                                class="text-white hover:text-pink-300 transition-colors">
                        Voting Platform
                    </x-nav-link>

                    <x-nav-link href="{{ route('events.index') }}"
                                :active="request()->routeIs('events.*')"
                                class="text-white hover:text-pink-300 transition-colors">
                        Discover Events
                    </x-nav-link>

                    <x-nav-link href="{{ route('tickets.my-tickets') }}"
                                :active="request()->routeIs('tickets.my-tickets')"
                                class="text-white hover:text-pink-300 transition-colors">
                        My Tickets
                    </x-nav-link>

                    <!-- Admin-only Links -->
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link href="{{ route('organizers.index') }}"
                                        :active="request()->routeIs('organizers.*')"
                                        class="text-white hover:text-pink-300 transition-colors">
                                Organizers
                            </x-nav-link>

                            <x-nav-link href="{{ route('admin.attendees.index') }}"
                                        :active="request()->routeIs('admin.attendees.*')"
                                        class="text-white hover:text-pink-300 transition-colors">
                                Attendees
                            </x-nav-link>

                            <x-nav-link href="{{ route('admin.vendors.index') }}"
                                        :active="request()->routeIs('admin.vendors.*')"
                                        class="text-white hover:text-pink-300 transition-colors">
                                Vendors
                            </x-nav-link>
                        @endif
                    @endauth

                    <!-- More Dropdown -->
                    <div class="relative" x-data="{ open: false }" x-cloak>
                        <button @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-white hover:text-pink-300 hover:border-pink-300 focus:outline-none transition duration-150 ease-in-out"
                                :class="{ 'border-pink-300 text-white': open }">
                            More
                            <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open"
                            x-transition
                            @click.away="open = false"
                            class="origin-top-left absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-gray-900 ring-1 ring-pink-500 ring-opacity-20 focus:outline-none z-50"
                            x-cloak>
                            <div class="py-1 flex flex-col">
                                <a href="{{ route('about') }}" class="block px-4 py-2 text-sm text-white hover:bg-pink-900 transition-colors">About Us</a>
                                <a href="{{ route('contact') }}" class="block px-4 py-2 text-sm text-white hover:bg-pink-900 transition-colors">Contact</a>
                                <a href="{{ route('help') }}" class="block px-4 py-2 text-sm text-white hover:bg-pink-900 transition-colors">Help Center</a>
                                <div class="border-t border-pink-500 border-opacity-20 my-1"></div>
                                <a href="{{ route('privacy') }}" class="block px-4 py-2 text-sm text-white hover:bg-pink-900 transition-colors">Privacy Policy</a>
                                <a href="{{ route('terms') }}" class="block px-4 py-2 text-sm text-white hover:bg-pink-900 transition-colors">Terms of Service</a>
                            </div>
                        </div>
                    </div>

                </div>

                </div>

                <!-- Right Side (Search + Auth) -->
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
                        <button @click="loginOpen = true" class="btn-secondary px-4 py-2 rounded-lg">
                            Log in
                        </button>
                        <button @click="signupOpen = true" class="btn-primary px-4 py-2 rounded-lg">
                            Sign up
                        </button>
                    </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="sm:hidden flex items-center">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-white hover:text-pink-300 focus:outline-none focus:text-pink-300 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="sm:hidden mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('voting.index') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Voting Platform</a>
                <a href="{{ route('events.index') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Discover Events</a>

                <!-- Admin-only Links -->
                @auth
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('organizers.index') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Organizers</a>
                <a href="{{ route('admin.attendees.index') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Attendees</a>
                <a href="{{ route('admin.vendors.index') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Vendors</a>
                @endif
                @endauth

                <li>
                    <a href="{{ route('tickets.my-tickets') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        My Tickets
                    </a>
                </li>



                <a href="{{ route('about') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">About Us</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Contact</a>
                <a href="{{ route('help') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Help Center</a>
                <a href="{{ route('privacy') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="block px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Terms of Service</a>

                @guest
                <div class="pt-4 pb-2 border-t border-pink-500 border-opacity-30">
                    <button @click="loginOpen = true; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Log in</button>
                    <button @click="signupOpen = true; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 text-white font-medium hover:bg-pink-900 rounded-md transition-colors">Sign up</button>
                </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Sign Up Modal -->
    <div x-show="signupOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4">
        <div class="modal-bg rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative border border-pink-500 border-opacity-30">
            <div class="modal-header rounded-t-lg p-4">
                <h2 class="text-2xl font-bold">Create Your Account</h2>
                <button @click="signupOpen = false" class="absolute top-4 right-4 text-white hover:text-pink-300 transition-colors z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 sm:p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">

                    <div class="mb-4 justify-center flex">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('img/Logo.png') }}"
                                alt="Javent"
                                class="h-20 w-auto">
                        </a>
                    </div>
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="name" value="Full Name *" class="text-white" />
                            <x-input id="name" type="text" name="name" :value="old('name')" required autofocus class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="email" value="Email Address *" class="text-white" />
                            <x-input id="email" type="email" name="email" :value="old('email')" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label for="phone" value="Phone Number" class="text-white" />
                        <x-input id="phone" type="tel" name="phone" :value="old('phone')" class="form-input w-full mt-1" />
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-6">
                        <x-label for="role" value="I want to register as *" class="text-white" />
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                            <label class="relative">
                                <input type="radio" name="role" value="attendee" class="sr-only peer" {{ old('role') == 'attendee' ? 'checked' : '' }} required>
                                <div class="role-card p-4 rounded-lg cursor-pointer transition-all peer-checked:border-pink-500">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-medium text-white">Attendee</span>
                                        <p class="text-xs text-gray-400 mt-1">Join events & vote</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="role" value="organizer" class="sr-only peer" {{ old('role') == 'organizer' ? 'checked' : '' }}>
                                <div class="role-card p-4 rounded-lg cursor-pointer transition-all peer-checked:border-pink-500">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="font-medium text-white">Organizer</span>
                                        <p class="text-xs text-gray-400 mt-1">Create events & contests</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="role" value="vendor" class="sr-only peer" {{ old('role') == 'vendor' ? 'checked' : '' }}>
                                <div class="role-card p-4 rounded-lg cursor-pointer transition-all peer-checked:border-pink-500">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-medium text-white">Vendor</span>
                                        <p class="text-xs text-gray-400 mt-1">Offer services</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Attendee Specific Fields -->
                    <div id="attendeeFields" class="hidden mt-4 space-y-4 p-4 specific-fields rounded-lg">
                        <h3 class="text-lg font-semibold text-pink-400">Attendee Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="occupation" value="Occupation" class="text-white" />
                                <x-input id="occupation" type="text" name="occupation" :value="old('occupation')" class="form-input w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="institution" value="Institution/Company" class="text-white" />
                                <x-input id="institution" type="text" name="institution" :value="old('institution')" class="form-input w-full mt-1" />
                            </div>
                        </div>
                        <div>
                            <x-label for="membership_number" value="Membership Number (Optional)" class="text-white" />
                            <x-input id="membership_number" type="text" name="membership_number" :value="old('membership_number')" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="attendee_type" value="Attendee Type" class="text-white" />
                            <select id="attendee_type" name="attendee_type" class="form-input block w-full mt-1 rounded-md shadow-sm">
                                <option value="">Select Type</option>
                                <option value="voter" {{ old('attendee_type') == 'voter' ? 'selected' : '' }}>Voter</option>
                                <option value="event-goer" {{ old('attendee_type') == 'event-goer' ? 'selected' : '' }}>Event Goer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Organizer Specific Fields -->
                    <div id="organizerFields" class="hidden mt-4 space-y-4 p-4 specific-fields rounded-lg">
                        <h3 class="text-lg font-semibold text-pink-400">Organizer Information</h3>
                        <div>
                            <x-label for="company_name" value="Company Name" class="text-white" />
                            <x-input id="company_name" type="text" name="company_name" :value="old('company_name')" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="website" value="Website" class="text-white" />
                            <x-input id="website" type="url" name="website" :value="old('website')" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="about" value="About Your Organization" class="text-white" />
                            <textarea id="about" name="about" rows="3" class="form-input block w-full mt-1 rounded-md shadow-sm">{{ old('about') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="address" value="Business Address" class="text-white" />
                                <x-input id="address" type="text" name="address" :value="old('address')" class="form-input w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="city" value="City" class="text-white" />
                                <x-input id="city" type="text" name="city" :value="old('city')" class="form-input w-full mt-1" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="state" value="State/Region" class="text-white" />
                                <x-input id="state" type="text" name="state" :value="old('state')" class="form-input w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="country" value="Country" class="text-white" />
                                <x-input id="country" type="text" name="country" :value="old('country')" class="form-input w-full mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Specific Fields -->
                    <div id="vendorFields" class="hidden mt-4 space-y-4 p-4 specific-fields rounded-lg">
                        <h3 class="text-lg font-semibold text-pink-400">Vendor Information</h3>
                        <div>
                            <x-label for="business_name" value="Business Name" class="text-white" />
                            <x-input id="business_name" type="text" name="business_name" :value="old('business_name')" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="contact_number" value="Business Contact Number" class="text-white" />
                            <x-input id="contact_number" type="tel" name="contact_number" :value="old('contact_number')" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="services_offered" value="Services Offered" class="text-white" />
                            <textarea id="services_offered" name="services_offered" rows="3" class="form-input block w-full mt-1 rounded-md shadow-sm" placeholder="Describe the services you offer...">{{ old('services_offered') }}</textarea>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <x-label for="password" value="Password *" class="text-white" />
                            <x-input id="password" type="password" name="password" required class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="password_confirmation" value="Confirm Password *" class="text-white" />
                            <x-input id="password_confirmation" type="password" name="password_confirmation" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <label class="flex items-center">
                            <x-checkbox name="terms" id="terms" required class="text-pink-600 focus:ring-pink-500" />
                            <span class="ms-2 text-sm text-gray-300">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-pink-400 hover:text-pink-300">'.__('Terms of Service').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-pink-400 hover:text-pink-300">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    </div>
                    @endif

                    <div class="mt-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Continue with Google -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 modal-bg text-gray-400">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('google.login') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 text-white bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 transition shadow-sm">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-3">
                            Continue with Google
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-400">
                        Already have an account?
                        <button type="button" @click="signupOpen = false; loginOpen = true" class="text-pink-400 hover:text-pink-300 font-medium transition-colors">
                            Log in
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-show="loginOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4">
        <div class="modal-bg rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative border border-pink-500 border-opacity-30">
            <div class="modal-header rounded-t-lg p-4">
                <h2 class="text-2xl font-bold">Welcome Back</h2>
                <button @click="loginOpen = false" class="absolute top-4 right-4 text-white hover:text-pink-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">

                    <div class="mb-4 justify-center flex">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('img/Logo.png') }}"
                                alt="Javent"
                                class="h-20 w-auto">
                        </a>
                    </div>
                <x-validation-errors class="mb-4" />

                @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-400 bg-green-900 bg-opacity-30 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <x-label for="login_email" value="Email Address" class="text-white" />
                        <x-input id="login_email" class="form-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
                    </div>
                    <div class="mb-4">
                        <x-label for="login_password" value="Password" class="text-white" />
                        <x-input id="login_password" class="form-input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    </div>
                    <div class="mb-4 flex items-center justify-between">
                        <label class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" class="text-pink-600 focus:ring-pink-500" />
                            <span class="ms-2 text-sm text-gray-300">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a class="text-sm text-pink-400 hover:text-pink-300 transition-colors" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <div class="mb-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Log In
                        </button>
                    </div>
                </form>

                <!-- Continue with Google -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 modal-bg text-gray-400">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('google.login') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 text-white bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 transition shadow-sm">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-3">
                            Continue with Google
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-400">
                        Don't have an account?
                        <button type="button" @click="loginOpen = false; signupOpen = true" class="text-pink-400 hover:text-pink-300 font-medium transition-colors">
                            Sign up
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Page Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-bg text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="footer-grid">
                <div>

                    <div class="mb-4">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('img/Logo.png') }}"
                                alt="Javent"
                                class="h-20 w-auto">
                        </a>
                    </div>


                    <p class="text-gray-400 mt-4 text-lg leading-relaxed">
                        Your complete universe for events, ticketing, and community engagement.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-pink-400">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('about') }}" class="hover:text-pink-300 transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-pink-300 transition-colors">Contact</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-pink-300 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-pink-300 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-pink-400">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('help') }}" class="hover:text-pink-300 transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-pink-300 transition-colors">Organizer Guides</a></li>
                        <li><a href="#" class="hover:text-pink-300 transition-colors">API Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-pink-400">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="https://web.facebook.com/people/Javapa-Limited/61579385817243/?_rdc=1&_rdr#" class="text-gray-400 hover:text-pink-300 transition-colors">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                        <a href="https://twitter.com/search?q=%23JavaPa&vertical=default" class="text-gray-400 hover:text-pink-300 transition-colors">
                        <span class="sr-only">X</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.39 3.61h2.56l-7.42 8.48 8.72 10.3h-6.87l-5.39-6.36-6.16 6.36H2.4l7.9-8.15L1.6 3.61h7.05l4.8 5.67 6.94-5.67z"/>
                        </svg>
                        </a>

                        <!-- LinkedIn -->
                        <a href="https://ke.linkedin.com/company/javapa" class="text-gray-400 hover:text-pink-300 transition-colors">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8h4V24h-4V8zm7.5 0h3.8v2.2h.05c.53-1 1.83-2.2 3.75-2.2 4 0 4.75 2.63 4.75 6.05V24h-4v-7.5c0-1.8-.03-4.1-2.5-4.1-2.5 0-2.88 1.95-2.88 3.97V24h-4V8z"/>
                        </svg>
                        </a>

                        <a href="https://www.instagram.com/jav.apa/" class="text-gray-400 hover:text-pink-300 transition-colors">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>
                            </svg>
                        </a>



                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 <a href="https://www.javapa.com/"><strong style="color: rgba(198, 0, 238, 1);">JavaPA LTD</strong></a>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Role selection functionality for signup modal
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const attendeeFields = document.getElementById('attendeeFields');
            const organizerFields = document.getElementById('organizerFields');
            const vendorFields = document.getElementById('vendorFields');

            function showRoleFields(role) {
                // Hide all fields first
                attendeeFields.classList.add('hidden');
                organizerFields.classList.add('hidden');
                vendorFields.classList.add('hidden');

                // Show fields based on selected role
                switch (role) {
                    case 'attendee':
                        attendeeFields.classList.remove('hidden');
                        break;
                    case 'organizer':
                        organizerFields.classList.remove('hidden');
                        break;
                    case 'vendor':
                        vendorFields.classList.remove('hidden');
                        break;
                }
            }

            // Add event listeners to role radio buttons
            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    showRoleFields(this.value);
                });
            });

            // Show fields based on initial value (for form validation errors)
            const initialRole = document.querySelector('input[name="role"]:checked');
            if (initialRole) {
                showRoleFields(initialRole.value);
            }

            // Handle modal transitions
            const signupModal = document.querySelector('[x-show="signupOpen"]');
            const loginModal = document.querySelector('[x-show="loginOpen"]');

            // Close modals when clicking outside
            [signupModal, loginModal].forEach(modal => {
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            this.style.display = 'none';
                        }
                    });
                }
            });
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
