@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
            <div class="prose prose-blue max-w-none">
                <p class="text-lg text-gray-600 mb-6">Last updated: {{ date('F j, Y') }}</p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Information We Collect</h2>
                <p class="text-gray-600 mb-4">
                    We collect information you provide directly to us, including when you create an account,
                    purchase tickets, participate in voting, or contact us for support.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. How We Use Your Information</h2>
                <p class="text-gray-600 mb-4">
                    We use the information we collect to provide, maintain, and improve our services,
                    process transactions, send you technical notices and support messages, and communicate with you about products, services, and events.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. Information Sharing</h2>
                <p class="text-gray-600 mb-4">
                    We do not sell, trade, or otherwise transfer your personal information to third parties without your consent,
                    except as described in this Privacy Policy or as required by law.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Data Security</h2>
                <p class="text-gray-600 mb-4">
                    We implement appropriate technical and organizational security measures designed to protect your personal information.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Your Rights</h2>
                <p class="text-gray-600 mb-4">
                    You have the right to access, correct, or delete your personal information. You can also object to or restrict certain processing of your data.
                </p>

                <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Contact Us</h2>
                <p class="text-gray-600">
                    If you have any questions about this Privacy Policy, please contact us at privacy@javent.com.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
