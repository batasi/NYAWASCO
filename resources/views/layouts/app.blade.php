<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route;
@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NYAWASCO</title>

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

    body {
        background: #FFFFFF;
        color: #000000;
    }

    main {
        min-height: calc(100vh - 200px);
    }

    /* Navigation gradient (sky blue) */
    .nav-gradient {
        background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
        color: #FFFFFF;
    }

    /* Body background gradient replaced with white */
    .body-gradient {
        background: #FFFFFF;
        color: #000000;
    }

    /* Modal backgrounds */
    .modal-bg {
        background: #FFFFFF;
        color: #000000;
    }

    .footer-bg {
        background: #FFFFFF;
        color: #000000;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        color: #FFFFFF;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .btn-secondary {
        background: #FFFFFF;
        color: #000000;
        border: 1px solid #FFD500;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #FFD500;
        color: #000000;
        transform: translateY(-2px);
    }

    /* Mobile navigation */
    .mobile-menu {
        background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
        color: #FFFFFF;
    }

    /* Form inputs */
    .form-input {
        background: #FFFFFF;
        border: 1px solid #000000;
        color: #000000;
    }

    .form-input:focus {
        background: #FFFFFF;
        border-color: #FFD500;
        box-shadow: 0 0 0 3px rgba(255, 213, 0, 0.3);
        outline: none;
    }

    .form-input::placeholder {
        color: rgba(0, 0, 0, 0.5);
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
    .text-yellow {
        color: #FFD500;
    }

    .hover-text-yellow:hover {
        color: #FFD500;
    }

    /* Modal header */
    .modal-header {
        background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
        color: #FFFFFF;
    }

    /* Modal body */
    .modal-b {
        background: #FFD500;
        color: #000000;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .modal-b:hover {
        background: #FFC700;
        transform: scale(1.02);
    }

    .modal-h {
        background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
        color: #FFFFFF;
        padding: 1rem 1.5rem;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1);
        letter-spacing: 0.5px;
    }

    .modal-h:hover {
        background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
        cursor: pointer;
    }

    /* Role selection cards */
    .role-card {
        background: #FFFFFF;
        border: 1px solid #000000;
        transition: all 0.3s ease;
    }

    .role-card:hover {
        border-color: #FFD500;
        background: rgba(255, 213, 0, 0.1);
    }

    .role-card.peer-checked\:border-yellow-500 {
        border-color: #FFD500;
        background: rgba(255, 213, 0, 0.1);
    }

    /* Specific fields */
    .specific-fields {
        background: rgba(255, 213, 0, 0.05);
        border: 1px solid rgba(255, 213, 0, 0.2);
    }

    /* Hero swiper */
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
        background: #000000;
        opacity: 0.6;
        width: 6px;
        height: 6px;
    }

    .hero-swiper .swiper-pagination-bullet-active {
        opacity: 1;
        background: #3B82F6;
    }

    .hero-swiper .swiper-button-prev,
    .hero-swiper .swiper-button-next {
        backdrop-filter: blur(5px);
        border: 1px solid rgba(0, 0, 0, 0.5);
    }

    .hero-swiper .swiper-button-prev:after,
    .hero-swiper .swiper-button-next:after {
        color: #1f2937 !important;
        font-weight: 500 !important;
    }

    .hero-swiper .swiper-button-prev:hover,
    .hero-swiper .swiper-button-next:hover {
        background: #3B82F6 !important;
        border-color: #3B82F6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-swiper {
            height: 500px !important;
            margin-top: -9.5rem;
        }

        .hero-swiper .swiper-button-prev,
        .hero-swiper .swiper-button-next {
            display: flex !important;
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

                            class="h-20 w-auto"> <!-- Increased to h-20 -->
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">

                    <!-- Core Links -->
                    <x-nav-link href="{{ route('home') }}"
                                :active="request()->routeIs('home')"
                                class="text-white hover:text-pink-300 transition-colors">
                        Home
                    </x-nav-link>

                    <!-- Admin-only Links -->
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link href="{{ route('organizers.index') }}"
                                        :active="request()->routeIs('organizers.*')"
                                        class="text-white hover:text-pink-300 transition-colors">
                                Billings
                            </x-nav-link>

                            <x-nav-link href="{{ route('admin.attendees.index') }}"
                                        :active="request()->routeIs('admin.attendees.*')"
                                        class="text-white hover:text-pink-300 transition-colors">
                                Meters
                            </x-nav-link>

                            <x-nav-link href="{{ route('admin.vendors.index') }}"
                                        :active="request()->routeIs('admin.vendors.*')"
                                        class="text-white hover:text-pink-300 transition-colors">

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

                <!-- <li>
                    <a href="{{ route('tickets.my-tickets') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        My Tickets
                    </a>
                </li> -->



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
   <footer class="text-white py-12" style="background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

                <!-- Logo & Description -->
                <!-- <div>
                    <a href="{{ route('home') }}" class="flex items-center mb-4">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                    <h6 class="text-white leading-relaxed">
                        Pace Driving School is committed to producing confident, responsible, and skilled drivers
                        through quality training, safety awareness, and professional instruction.
                    </h6>
                </div> -->

                <!-- Quick Links -->
                <!-- <div>
                    <h4 class="text-lg font-semibold text-primary-400 mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-300 transition">Home</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-primary-300 transition">About Us</a></li>
                        <li><a href="#courses" class="hover:text-primary-300 transition">Our Courses</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-primary-300 transition">Contact</a></li>
                        <li><a href="#testimonials" class="hover:text-primary-300 transition">Testimonials</a></li>
                    </ul>
                </div> -->

                <!-- Contact Info -->
                <!-- <div>
                    <h4 class="text-lg font-semibold text-primary-400 mb-4">Contact Us</h4>
                    <ul class="space-y-3">
                        <li>
                            <i class="fa-solid fa-location-dot text-primary-400 mr-2"></i>
                            <span>Eastleigh,Suncity - Nairobi</span><br>
                            <i class="fa-solid fa-location-dot text-primary-400 mr-2"></i>
                            <span>Ringroad Kilimani - Nairobi</span><br>
                            <i class="fa-solid fa-location-dot text-primary-400 mr-2"></i>
                            <span>Naivasha Road - Nairobi</span><br>
                            <i class="fa-solid fa-location-dot text-primary-400 mr-2"></i>
                            <span>Mpaka Road, Parklands - Nairobi</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone text-primary-400 mr-2"></i>
                            <a href="tel:+254728725559" class="hover:text-primary-300 transition">+254 728 725 559</a><br>
                            <i class="fa-solid fa-phone text-primary-400 mr-2"></i>
                            <a href="tel:+254714725559" class="hover:text-primary-300 transition">+254 714 725 559</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope text-primary-400 mr-2"></i>
                            <a href="mailto:info@pacedrivingschool.co.ke" class="hover:text-primary-300 transition">
                                info@pacedrivingschool.co.ke
                            </a>
                        </li>
                    </ul>
                </div> -->

                <!-- Social Links -->
                <!-- <div>
                    <h4 class="text-lg font-semibold text-primary-400 mb-4">Follow Us</h4>
                    <div class="flex space-x-5">
                        <a href="https://www.facebook.com/pacedrivingschool" target="_blank" class="hover:text-primary-300 transition">
                            <i class="fa-brands fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="https://www.instagram.com/pacedrivingschool" target="_blank" class="hover:text-primary-300 transition">
                            <i class="fa-brands fa-instagram text-2xl"></i>
                        </a>
                        <a href="https://www.twitter.com/pacedriving" target="_blank" class="hover:text-primary-300 transition">
                            <i class="fa-brands fa-x-twitter text-2xl"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/pacedrivingschool" target="_blank" class="hover:text-primary-300 transition">
                            <i class="fa-brands fa-linkedin-in text-2xl"></i>
                        </a>
                    </div>
                </div> -->
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm">
                <p>
                    &copy; 2025
                    <strong>NYAWASCO</strong>.
                    All rights reserved. Developed by
                    <a href="#" class="text-primary-400 hover:text-primary-300 font-semibold">....</a>.
                </p>
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
