{{-- resources/views/publication-single.blade.php --}}
@extends('layouts.app')

@section('title', $publication['title'] . ' - NYAWASCO')

@section('content')
@php use Illuminate\Support\Str; @endphp

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('publications') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">
                        Publications
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-blue-600 md:ml-2">
                        {{ Str::limit($publication['title'], 30) }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Publication Header -->
    <section class="mb-12">
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                <div>
                    <div class="flex items-center mb-4">
                        <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                            Press Release
                        </span>
                        <span class="ml-2 text-sm text-gray-500">8th January, 2026</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Nyamira County Unveils NYAWASCO Board, Paves Way for Universal Water Access
                    </h1>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="text-sm">5 min read</span>
                    </div>
                </div>

            </div>

            <!-- Publication Meta -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 border-t border-gray-200 pt-6">
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    <span>1,250 views</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    <span>45 downloads</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-share-alt mr-2"></i>
                    <span>Share:</span>
                    <div class="flex ml-2 space-x-2">
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-blue-400 hover:text-blue-600">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-blue-700 hover:text-blue-900">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Publication Content -->
    <section class="mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                    <!-- Executive Summary -->
                    <div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                        <p class="text-lg font-semibold text-gray-800">
                            Nyamira County has made a significant step towards achieving universal access to clean and safe water for the people with the inauguration of the Nyamira Water and Sanitation Company (NYAWASCO) Board.
                        </p>
                    </div>

                    <!-- Main Content -->
                    <div class="prose prose-lg max-w-none text-gray-700">
                        <p class="text-gray-700 leading-relaxed mb-6">
                            On Thursday, Governor Amos Kimwomi Nyaribo officially unveiled the 9-member board of directors, tasked with ensuring reliable water connectivity and enhancing sanitation management in the county.
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            The move follows Nyamira's declustering from Gusii Water and Sanitation Company (GWASCO) due to challenges like water disruptions and unpaid bills.
                        </p>

                        <!-- Governor's Quote -->
                        <div class="my-8 p-6 bg-gray-50 border-l-4 border-blue-500 rounded-r-lg">
                            <p class="text-xl italic text-gray-700 leading-relaxed">
                                "Water is life, and it is crucial as we work together to make it accessible to every household."
                            </p>
                            <p class="mt-4 font-semibold text-gray-900">— Governor Amos Kimwomi Nyaribo</p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            Speaking at the ceremony, Governor Nyaribo emphasized the importance of water accessibility, saying, "Water is life, and it is crucial as we work together to make it accessible to every household."
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            He also issued a stern warning to perpetrators of illegal water connections, urging the board to take action against those caught, emphasizing the need for smart water meters to improve billing and revenue collection.
                        </p>

                        <!-- Governor's Directive -->
                        <div class="my-8 p-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">Governor's Directive</p>
                            <p class="text-gray-700 leading-relaxed">
                                "I want you to deal with the culprits who steal water so that we streamline our revenue streams," he said.
                            </p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            The County CEO further urged the board of directors to prioritize revenue collection and cost cutting measures to ensure the company's sustainability.
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            "Let's strive to improve our revenue collection for the company to survive," Governor Nyaribo said, emphasizing the need for efficient management.
                        </p>

                        <!-- Sustainable Energy Initiative -->
                        <div class="my-8 p-6 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">Sustainable Energy Initiative</p>
                            <p class="text-gray-700 leading-relaxed">
                                He also advised the board to control expenses, particularly on power supply, and explore alternative energy sources like solar power to avoid disruption in water supply. "Let's strive to get funds and solarize," he said, highlighting the potential for renewable energy to support the company's operations.
                            </p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            Governor Nyaribo noted that the board of directors key priorities will include enhancing water connectivity, managing pending bills, and improving customer service, as they work together to ensure universal access to clean, safe, and sufficient water for the people.
                        </p>

                        <!-- Training Program -->
                        <div class="my-8 p-6 bg-purple-50 border-l-4 border-purple-500 rounded-r-lg">
                            <div class="flex items-start">
                                <div>
                                    <p class="text-lg font-semibold text-gray-800 mb-2">Board Training Program</p>
                                    <p class="text-gray-700 leading-relaxed">
                                        The members will proceed on a five day training to learn on best practices in water governance and execution of functions.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            The CECM for Environment, Dr. John Matiang'i reiterated the need to prioritize water supply and expressed optimism in the new board's commitment to address the impending water access for Nyamira residents.
                        </p>

                        <!-- Board Members Section -->
                        <div class="my-10">
                            <h4 class="text-xl font-bold text-gray-900 mb-6">NYAWASCO Board of Directors:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach([
                                    'Dr. Asenath Maobe (Finance)',
                                    'Richard Onyinkwa (Water)',
                                    'Naom Nyaechero',
                                    'Dr. Ogaro Kaunda',
                                    'Dr. Carolyne Matara',
                                    'James Bundi',
                                    'Stephen Ouko',
                                    'Joseph Arama',
                                    'Victorinah Makori'
                                ] as $member)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-gray-700">{{ $member }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Establishment Purpose -->
                        <div class="my-8 p-6 bg-blue-50 rounded-lg">
                            <p class="text-gray-700 leading-relaxed">
                                The establishment of NYAWASCO aims to bring improved water supply and sanitation management to Nyamira County, benefiting residents and supporting local development.
                            </p>
                        </div>

                        <!-- Attendees Section -->
                        <div class="my-10">
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Attended by:</h4>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h5 class="font-semibold text-gray-900 mb-2">CECMs in Attendance:</h5>
                                        <ul class="text-gray-600 text-sm space-y-1">
                                            <li>• Dr. John Matiang'i (Water, Environment)</li>
                                            <li>• Alice Manoti (Gender, Youth, Sports)</li>
                                            <li>• Stephen Oboso (Lands)</li>
                                            <li>• Kennedy Angwenyi (Public Service Management)</li>
                                            <li>• Dr. Grace Nyamongo (Roads, Public Works)</li>
                                            <li>• Bernard Maina (Trade)</li>
                                            <li>• Dr. Peris Mong'are (Agriculture)</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 mb-2">Chief Officers & Others:</h5>
                                        <ul class="text-gray-600 text-sm space-y-1">
                                            <li>• Richard Onyinkwa (Water)</li>
                                            <li>• Dr. Ombogo Marwanga (Livestock Production)</li>
                                            <li>• Dr. Mercy Motanya (Gender, Youth, Sports)</li>
                                            <li>• Moenga Momanyi (Trade)</li>
                                            <li>• Jones Bitange (Chief of Staff)</li>
                                            <li>• Among other county officials</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Key Takeaways -->
                        <div class="my-10 border-t border-gray-200 pt-8">
                            <h4 class="text-xl font-bold text-gray-900 mb-6">Key Takeaways:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-users text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">New Board Inaugurated</h5>
                                        <p class="text-gray-600 text-sm">9-member board appointed to lead NYAWASCO</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bullseye text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Universal Water Access</h5>
                                        <p class="text-gray-600 text-sm">Goal to provide water to every household</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-solar-panel text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Solar Energy Focus</h5>
                                        <p class="text-gray-600 text-sm">Transition to renewable energy for operations</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-graduation-cap text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Training Program</h5>
                                        <p class="text-gray-600 text-sm">5-day training for board members</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Related Publications -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Publications</h3>
                    <div class="space-y-4">
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">Annual Report 2025</h4>
                            <p class="text-sm text-gray-600">Coming March 2025</p>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">Strategic Plan 2025-2030</h4>
                            <p class="text-sm text-gray-600">Coming February 2025</p>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">Water Tariff Policy</h4>
                            <p class="text-sm text-gray-600">Coming April 2025</p>
                        </a>
                    </div>
                </div>

                <!-- Publication Details -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Publication Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-semibold">Press Release</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-semibold">8th January, 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-semibold">Official Announcement</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reading Time:</span>
                            <span class="font-semibold">5 minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Word Count:</span>
                            <span class="font-semibold">650 words</span>
                        </div>
                    </div>
                </div>

                <!-- Media Contact -->
                <div class="bg-gradient-to-b from-blue-600 to-blue-700 rounded-xl p-6 text-white">
                    <h3 class="text-lg font-semibold mb-3">Media Contact</h3>
                    <p class="text-blue-100 text-sm mb-4">
                        For media inquiries about this publication
                    </p>
                    <a href="mailto:media@nyawasco.co.ke" class="inline-flex items-center justify-center w-full px-4 py-2 bg-white text-blue-700 rounded-lg font-semibold hover:bg-blue-50 transition text-sm">
                        <i class="fas fa-envelope mr-2"></i>
                        Email Media Desk
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Publications -->
    <div class="text-center">
        <a href="{{ route('publications') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Publications
        </a>
    </div>

</main>
@endsection
