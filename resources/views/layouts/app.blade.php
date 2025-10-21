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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
    </style>
</head>

<body x-data="{ signupOpen: false, loginOpen: false, intendedUrl: '{{ route('organizers.index') }}' }" class="font-sans antialiased bg-gray-50">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white bg-opacity-95 backdrop-blur-md shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">Javent</a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link href="{{ route('voting.index') }}" :active="request()->routeIs('voting.*')">
                            Voting Platform
                        </x-nav-link>
                        <x-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.*')">
                            Discover Events
                        </x-nav-link>

                       

                        <!-- Admin-only Links -->
                        @auth
                        @if(auth()->user()->hasRole('admin'))
                        <x-nav-link href="{{ route('organizers.index') }}" :active="request()->routeIs('organizers.*')">
                            Organizers
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.attendees.index') }}" :active="request()->routeIs('admin.attendees.*')">
                            Attendees
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.vendors.index') }}" :active="request()->routeIs('admin.vendors.*')">
                            Vendors
                        </x-nav-link>
                        @endif
                        @endauth

                        <!-- More Dropdown -->
                        <div class="relative" x-data="{ open: false }" x-cloak>
                            <button
                                @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out"
                                :class="{ 'border-blue-500 text-gray-900': open }">
                                More
                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                @click.away="open = false"
                                class="origin-top-left absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                x-cloak>
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
                        <button @click="loginOpen = true" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                            Log in
                        </button>
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
    <div x-show="signupOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
            <button @click="signupOpen = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Your Account</h2>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="name" value="Full Name *" />
                            <x-input id="name" type="text" name="name" :value="old('name')" required autofocus class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="email" value="Email Address *" />
                            <x-input id="email" type="email" name="email" :value="old('email')" required class="w-full mt-1" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label for="phone" value="Phone Number" />
                        <x-input id="phone" type="tel" name="phone" :value="old('phone')" class="w-full mt-1" />
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-6">
                        <x-label for="role" value="I want to register as *" />
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
                            <label class="relative">
                                <input type="radio" name="role" value="attendee" class="sr-only peer" {{ old('role') == 'attendee' ? 'checked' : '' }} required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 peer-checked:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 peer-checked:text-blue-900">Attendee</span>
                                        <p class="text-xs text-gray-500 mt-1">Join events & vote</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="role" value="organizer" class="sr-only peer" {{ old('role') == 'organizer' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="font-medium text-gray-900 peer-checked:text-green-900">Organizer</span>
                                        <p class="text-xs text-gray-500 mt-1">Create events & contests</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="role" value="vendor" class="sr-only peer" {{ old('role') == 'vendor' ? 'checked' : '' }}>
                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-300">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 peer-checked:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 peer-checked:text-purple-900">Vendor</span>
                                        <p class="text-xs text-gray-500 mt-1">Offer services</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Attendee Specific Fields -->
                    <div id="attendeeFields" class="hidden mt-4 space-y-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900">Attendee Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="occupation" value="Occupation" />
                                <x-input id="occupation" type="text" name="occupation" :value="old('occupation')" class="w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="institution" value="Institution/Company" />
                                <x-input id="institution" type="text" name="institution" :value="old('institution')" class="w-full mt-1" />
                            </div>
                        </div>
                        <div>
                            <x-label for="membership_number" value="Membership Number (Optional)" />
                            <x-input id="membership_number" type="text" name="membership_number" :value="old('membership_number')" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="attendee_type" value="Attendee Type" />
                            <select id="attendee_type" name="attendee_type" class="block w-full mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">Select Type</option>
                                <option value="voter" {{ old('attendee_type') == 'voter' ? 'selected' : '' }}>Voter</option>
                                <option value="event-goer" {{ old('attendee_type') == 'event-goer' ? 'selected' : '' }}>Event Goer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Organizer Specific Fields -->
                    <div id="organizerFields" class="hidden mt-4 space-y-4 p-4 bg-green-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-900">Organizer Information</h3>
                        <div>
                            <x-label for="company_name" value="Company Name" />
                            <x-input id="company_name" type="text" name="company_name" :value="old('company_name')" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="website" value="Website" />
                            <x-input id="website" type="url" name="website" :value="old('website')" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="about" value="About Your Organization" />
                            <textarea id="about" name="about" rows="3" class="block w-full mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">{{ old('about') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="address" value="Business Address" />
                                <x-input id="address" type="text" name="address" :value="old('address')" class="w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="city" value="City" />
                                <x-input id="city" type="text" name="city" :value="old('city')" class="w-full mt-1" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="state" value="State/Region" />
                                <x-input id="state" type="text" name="state" :value="old('state')" class="w-full mt-1" />
                            </div>
                            <div>
                                <x-label for="country" value="Country" />
                                <x-input id="country" type="text" name="country" :value="old('country')" class="w-full mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Specific Fields -->
                    <div id="vendorFields" class="hidden mt-4 space-y-4 p-4 bg-purple-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-900">Vendor Information</h3>
                        <div>
                            <x-label for="business_name" value="Business Name" />
                            <x-input id="business_name" type="text" name="business_name" :value="old('business_name')" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="contact_number" value="Business Contact Number" />
                            <x-input id="contact_number" type="tel" name="contact_number" :value="old('contact_number')" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="services_offered" value="Services Offered" />
                            <textarea id="services_offered" name="services_offered" rows="3" class="block w-full mt-1 border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" placeholder="Describe the services you offer...">{{ old('services_offered') }}</textarea>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <x-label for="password" value="Password *" />
                            <x-input id="password" type="password" name="password" required class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="password_confirmation" value="Confirm Password *" />
                            <x-input id="password_confirmation" type="password" name="password_confirmation" required class="w-full mt-1" />
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <label class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <span class="ms-2 text-sm text-gray-600">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    </div>
                    @endif

                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Continue with Google -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('google.login') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-3">
                            Continue with Google
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Already have an account?
                        <button type="button" @click="signupOpen = false; loginOpen = true" class="text-blue-600 hover:underline font-medium">
                            Log in
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-show="loginOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md relative">
            <button @click="loginOpen = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Welcome Back</h2>

                <x-validation-errors class="mb-4" />

                @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <x-label for="login_email" value="Email Address" />
                        <x-input id="login_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
                    </div>
                    <div class="mb-4">
                        <x-label for="login_password" value="Password" />
                        <x-input id="login_password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    </div>
                    <div class="mb-4 flex items-center justify-between">
                        <label class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <span class="ms-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <div class="mb-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                            Log In
                        </button>
                    </div>
                </form>

                <!-- Continue with Google -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('google.login') }}"
                            class="inline-flex items-center justify-center w-full px-4 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-3">
                            Continue with Google
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Don't have an account?
                        <button type="button" @click="loginOpen = false; signupOpen = true" class="text-blue-600 hover:underline font-medium">
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
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Javent</h3>
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
                <p>&copy; 2025 JavaPA LTD. All rights reserved.</p>
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