@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">About Javent</h1>
            <div class="prose prose-blue max-w-none">
                <p class="text-lg text-gray-600 mb-6">
                    Javent is your complete universe for events, ticketing, and community engagement.
                    We connect organizers with attendees and provide powerful tools for creating unforgettable experiences.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">Our Mission</h2>
                <p class="text-gray-600 mb-6">
                    To revolutionize the event industry by providing a seamless platform that empowers organizers
                    and delights attendees through innovative technology and exceptional user experiences.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">What We Offer</h2>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-900 mb-2">For Organizers</h3>
                        <p class="text-blue-800 text-sm">Complete event management tools, ticketing solutions, and analytics.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-900 mb-2">For Attendees</h3>
                        <p class="text-green-800 text-sm">Easy event discovery, secure ticketing, and seamless experiences.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
