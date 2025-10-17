@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h1>
            <div class="prose prose-blue max-w-none">
                <p class="text-lg text-gray-600 mb-6">Last updated: {{ date('F j, Y') }}</p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Acceptance of Terms</h2>
                <p class="text-gray-600 mb-4">
                    By accessing and using EventSphere, you accept and agree to be bound by the terms and provision of this agreement.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Use License</h2>
                <p class="text-gray-600 mb-4">
                    Permission is granted to temporarily use EventSphere for personal, non-commercial transitory viewing only.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. User Accounts</h2>
                <p class="text-gray-600 mb-4">
                    When you create an account with us, you must provide accurate and complete information. You are responsible for safeguarding your account.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Event Tickets and Voting</h2>
                <p class="text-gray-600 mb-4">
                    All ticket sales are final unless otherwise stated. Voting results are determined by participant votes and are final.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Prohibited Uses</h2>
                <p class="text-gray-600 mb-4">
                    You may not use our service for any illegal or unauthorized purpose. You must not violate any laws in your jurisdiction.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Termination</h2>
                <p class="text-gray-600 mb-4">
                    We may terminate or suspend your account immediately, without prior notice, for conduct that we believe violates these Terms.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Changes to Terms</h2>
                <p class="text-gray-600">
                    We reserve the right to modify these terms at any time. We will provide notice of significant changes.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
