@extends('layouts.app')

@section('title', 'Pricing & Plans - Javent')

@section('content')
<div class="min-h-screen modal-bg py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">Simple, Transparent Pricing</h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Whether you're hosting free community events or premium experiences, Javent offers flexible pricing 
                designed to help you succeed. No hidden fees, no monthly contracts.
            </p>
        </div>

        <!-- Free Events Banner -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 mb-16 text-center shadow-2xl">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-white mb-4">Free for Community Events!</h2>
                <p class="text-lg text-purple-100 mb-6 leading-relaxed">
                    Javent believes in supporting communities. Host free events for your organization, 
                    community cause, friends, or family with our fully-featured platform at absolutely no cost.
                </p>
                <div class="bg-white bg-opacity-20 rounded-lg p-6 inline-block">
                    <p class="text-white font-semibold text-lg">
                        Perfect for: Community gatherings • Fundraisers • Workshops • Meetups • Non-profit events
                    </p>
                </div>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <!-- Events & Ticketing Plan -->
            <div class="bg-black rounded-2xl shadow-2xl border border-purple-500 overflow-hidden hover:scale-105 transition-transform duration-300">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-center">
                    <h3 class="text-2xl font-bold text-white">Events & Ticketing</h3>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-white">4% - 5%</span>
                        <span class="text-purple-200 block mt-2">Per Ticket Sold</span>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">4% for revenue over KSh 1,000,000</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">5% for revenue up to KSh 1,000,000</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Includes credit card processing fees</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Send emails to attendees</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Add coupons & discount codes</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Team management & co-organizers</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Dedicated support personnel</span>
                        </li>
                    </ul>
                    <a href="{{ route('events.index') ?? '/events' }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 block text-center">
                        Browse Events
                    </a>
                </div>
            </div>

            <!-- Voting Programs Plan -->
            <div class="bg-black rounded-2xl shadow-2xl border border-pink-500 overflow-hidden hover:scale-105 transition-transform duration-300 transform scale-105 relative">
                <div class="absolute top-4 right-4">
                    <span class="bg-pink-500 text-white text-sm font-bold px-3 py-1 rounded-full">Most Popular</span>
                </div>
                <div class="bg-gradient-to-r from-pink-500 to-red-600 p-6 text-center">
                    <h3 class="text-2xl font-bold text-white">Voting Programs</h3>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-white">20% - 30%</span>
                        <span class="text-pink-200 block mt-2">Per Vote Processed</span>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">20% for revenue over KSh 1,000,000</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">30% for revenue up to KSh 1,000,000</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Unlimited voting categories</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Unlimited contestants/nominees</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Real-time results & analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Email communication with contestants</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Dedicated voting support team</span>
                        </li>
                    </ul>
                    <a href="{{ route('voting.index') ?? '/voting' }}" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 block text-center">
                        Explore Voting
                    </a>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="bg-black rounded-2xl shadow-2xl border border-green-500 overflow-hidden hover:scale-105 transition-transform duration-300">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6 text-center">
                    <h3 class="text-2xl font-bold text-white">Enterprise</h3>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-white">Custom</span>
                        <span class="text-green-200 block mt-2">Tailored Solutions</span>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Custom pricing for high-volume events</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">White-label solutions available</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">API access & custom integrations</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Dedicated account manager</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Priority 24/7 support</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Custom reporting & analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-300">Bulk event management tools</span>
                        </li>
                    </ul>
                    <a href="{{ route('contact') ?? '/contact' }}" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 block text-center">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment & Payouts Section -->
        <div class="bg-black rounded-2xl shadow-2xl border border-purple-500 p-8 mb-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-4"> Fast & Flexible Payouts</h2>
                <p class="text-xl text-gray-300 max-w-4xl mx-auto">
                    Get paid quickly and manage your funds with complete transparency
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-900 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-purple-400 mb-4">For Event Organizers</h3>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Funds available for withdrawal within 2 days of event publication</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Withdraw any amount needed, up to once daily</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Complete event reports provided after conclusion</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-gray-900 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-pink-400 mb-4">Important Notice</h3>
                    <div class="bg-yellow-500 bg-opacity-20 border border-yellow-500 rounded-lg p-4">
                        <p class="text-yellow-200 text-sm">
                            <strong>Refund Policy:</strong> Organizers are responsible for handling event cancellations and refunds. 
                            Javent provides the platform but does not manage refund processes for paid events.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Javent -->
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-white mb-8">Why Trust Javent</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">No Monthly Fees</h3>
                    <p class="text-gray-400">Pay only when you sell. No hidden costs or subscription fees.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Lightning Fast</h3>
                    <p class="text-gray-400">Quick setup, instant payouts, and real-time analytics.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">24/7 Support</h3>
                    <p class="text-gray-400">Dedicated support team ready to help you succeed.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-12">
                <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
                <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of organizers who trust Javent for their events and voting programs
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') ?? '/register' }}" class="bg-white text-purple-600 hover:bg-gray-100 font-bold py-4 px-8 rounded-lg transition duration-300 text-lg">
                        Start Free Today
                    </a>
                    <a href="{{ route('contact') ?? '/contact' }}" class="border-2 border-white text-white hover:bg-white hover:text-purple-600 font-bold py-4 px-8 rounded-lg transition duration-300 text-lg">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection