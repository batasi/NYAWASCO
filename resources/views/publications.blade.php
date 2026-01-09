{{-- resources/views/publications.blade.php --}}
@extends('layouts.app')

@section('title', 'News & Publications - NYAWASCO')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Page Header -->
    <section class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-blue-700 mb-4">News & Publications</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Official announcements, press releases, and publications from NYAWASCO
        </p>
    </section>

    <!-- Featured Publication Card -->
    <section class="mb-16">
        <div class="flex items-center mb-8">

            <div class="ml-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Featured Publication</h2>
                <p class="text-gray-600">Latest official announcement from NYAWASCO</p>
            </div>
        </div>

        <!-- Featured Publication Card -->
        <a href="{{ route('publication.show', 'nyamira-unveils-nyawasco-board') }}"
           class="block bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100 hover:shadow-xl transition duration-300">
            <div class="md:flex">
                <!-- Publication Image/Thumbnail -->
                <div class="md:w-2/5">
                    <div class="relative h-64 md:h-full">
                        <!-- Main Image -->
                        <img src="/img/water-hero-1.jpeg"
                             alt="NYAWASCO Board Inauguration Ceremony"
                             class="w-full h-full object-cover">

                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                        <!-- Image Badge -->


                        <!-- Image Caption -->
                        <div class="absolute bottom-4 left-4 right-4">
                            <p class="text-white text-sm font-medium">
                                Governor Amos Nyaribo inaugurating the NYAWASCO Board
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Publication Details -->
                <div class="md:w-2/3 p-8">
                    <div class="flex items-center mb-4">
                        <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                            Featured
                        </span>
                        <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                            Press Release
                        </span>
                        <span class="ml-2 text-xs text-gray-500">8th January, 2026</span>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        Nyamira County Unveils NYAWASCO Board, Paves Way for Universal Water Access
                    </h3>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Nyamira County has made a significant step towards achieving universal access to clean and safe water for the people with the inauguration of the Nyamira Water and Sanitation Company (NYAWASCO) Board.
                    </p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2"></i>
                            <span>5 min read</span>
                        </div>
                        <div class="flex items-center text-blue-600 font-semibold">
                            Read Full Story
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </section>

    <!-- All Publications -->
    <section class="mb-20">
        <div class="flex items-center mb-8">

            <div class="ml-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">All Publications</h2>
                <p class="text-gray-600">Browse through our collection of official documents and reports</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Publication 1 - Board Inauguration -->
            <a href="{{ route('publication.show', 'nyamira-unveils-nyawasco-board') }}"
               class="block bg-white shadow-md rounded-xl p-6 border border-gray-100 hover:shadow-lg transition hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                        Press Release
                    </span>
                    <span class="ml-2 text-xs text-gray-500">8th Jan, 2026</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">NYAWASCO Board Inaugurated</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Governor Amos Kimwomi Nyaribo unveils 9-member board to enhance water access in Nyamira County.
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>5 min read</span>
                    </div>
                    <span class="text-blue-600 font-semibold">Read →</span>
                </div>
            </a>

            <!-- Publication 2 (Coming Soon) -->
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                        Annual Report
                    </span>
                    <span class="ml-2 text-xs text-gray-500">Coming Soon</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">NYAWASCO Annual Report 2025</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Comprehensive overview of our achievements, financial performance, and future plans.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Expected: March 2025</span>
                </div>
            </div>

            <!-- Publication 3 (Coming Soon) -->
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        Strategic Plan
                    </span>
                    <span class="ml-2 text-xs text-gray-500">Coming Soon</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">2025-2030 Strategic Plan</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Our roadmap for sustainable water service delivery and infrastructure development.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Expected: February 2025</span>
                </div>
            </div>

            <!-- Publication 4 (Coming Soon) -->
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                        Policy Document
                    </span>
                    <span class="ml-2 text-xs text-gray-500">Coming Soon</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Water Tariff Policy</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Official document outlining water pricing and billing policies for Nyamira County.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Expected: April 2025</span>
                </div>
            </div>

            <!-- Publication 5 (Coming Soon) -->
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                        Financial Report
                    </span>
                    <span class="ml-2 text-xs text-gray-500">Coming Soon</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Quarterly Financial Report</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Detailed financial performance and budget utilization report for Q4 2025.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Expected: January 2025</span>
                </div>
            </div>

            <!-- Publication 6 (Coming Soon) -->
            <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <span class="px-3 py-1 bg-teal-100 text-teal-700 text-xs font-semibold rounded-full">
                        Sustainability Report
                    </span>
                    <span class="ml-2 text-xs text-gray-500">Coming Soon</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Environmental Sustainability Report</h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    Assessment of our environmental impact and sustainability initiatives.
                </p>
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span>Expected: May 2025</span>
                </div>
            </div>
        </div>

        <!-- View More Button -->
        <div class="text-center mt-12">
            <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition inline-flex items-center">
                <i class="fas fa-history mr-2"></i>
                View Archive
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </section>

    <!-- Media Contact -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 md:p-10 text-white">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="lg:w-2/3">
                <h3 class="text-2xl md:text-3xl font-bold mb-4">Media Inquiries</h3>
                <p class="text-blue-100 mb-6 max-w-2xl">
                    For media inquiries, interview requests, or additional information about NYAWASCO publications, please contact our Communications Department.
                </p>
                <div class="flex flex-wrap gap-6">

                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="text-sm text-blue-200">Phone</div>
                            <div class="font-semibold">+254 787 080 455</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/3">
                <a href="mailto:media@nyawasco.co.ke" class="inline-flex items-center justify-center w-full px-6 py-3 bg-white text-blue-700 rounded-lg font-semibold hover:bg-blue-50 transition">
                    Contact Media Desk
                </a>
            </div>
        </div>
    </section>

</main>
@endsection
