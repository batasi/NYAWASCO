@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Help Center</h1>
            <div class="prose prose-blue max-w-none">
                <p class="text-lg text-gray-600 mb-8">
                    Find answers to common questions and learn how to make the most of EventSphere.
                </p>

                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Getting Started</h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">How do I create an account?</h3>
                                <p class="text-gray-600">Click the "Sign Up" button in the top navigation and follow the registration process.</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Is EventSphere free to use?</h3>
                                <p class="text-gray-600">Yes, creating an account and browsing events is completely free. Event organizers may charge for tickets.</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Events & Tickets</h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">How do I purchase tickets?</h3>
                                <p class="text-gray-600">Browse events, select your preferred event, choose tickets, and complete the checkout process.</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Can I get a refund?</h3>
                                <p class="text-gray-600">Refund policies are set by event organizers. Please check the event details for specific refund information.</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Voting</h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">How does voting work?</h3>
                                <p class="text-gray-600">Browse active voting contests, view nominees, and cast your votes according to the contest rules.</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Can I change my vote?</h3>
                                <p class="text-gray-600">This depends on the contest settings. Some contests allow vote changes while others don't.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Still Need Help?</h2>
                        <p class="text-gray-600 mb-4">Can't find what you're looking for? Our support team is here to help.</p>
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
