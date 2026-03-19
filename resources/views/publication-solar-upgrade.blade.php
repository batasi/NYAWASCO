{{-- resources/views/publication-solar-upgrade.blade.php --}}
@extends('layouts.app')

@section('title', 'Nyamira Water Project Set for Solar Upgrade Plans - NYAWASCO')

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
                        Nyamira Water Project Solar Upgrade
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
                        <span class="px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                            Press Release
                        </span>
                        <span class="ml-2 text-sm text-gray-500">19th March, 2026</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Nyamira Water Project Set for Solar Upgrade Plans
                    </h1>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="text-sm">4 min read</span>
                    </div>
                </div>
            </div>

            <!-- Publication Meta -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 border-t border-gray-200 pt-6">
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    <span>850 views</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    <span>32 downloads</span>
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
                    <div class="mb-8 p-6 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                        <p class="text-lg font-semibold text-gray-800">
                            Nyamira County and Lake Victoria South Water Works Development Agency are partnering in solarising the water supply infrastructure, Governor Amos Nyaribo has said.
                        </p>
                    </div>

                    <!-- Main Content -->
                    <div class="prose prose-lg max-w-none text-gray-700">
                        <p class="text-gray-700 leading-relaxed mb-6">
                            The County CEO reaffirmed vitalising the Nyamira Water Supply Project, as preparations intensify for the anticipated commissioning during an upcoming presidential visit.
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            Speaking during a consultative meeting held at the Water Boardroom, Governor Nyaribo emphasized the urgent need to make the water utility sustainable through strategic investments, particularly in solar energy.
                        </p>

                        <!-- Governor's Quote -->
                        <div class="my-8 p-6 bg-gray-50 border-l-4 border-green-500 rounded-r-lg">
                            <p class="text-xl italic text-gray-700 leading-relaxed">
                                "For a water company to operate effectively, we must address the cost of power. Electricity is expensive, and that is why we are focusing on solarization of our water pumps."
                            </p>
                            <p class="mt-4 font-semibold text-gray-900">— Governor Amos Kimwomi Nyaribo</p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            He noted that the County is working closely with the Lake Victoria South Water Works Development Agency (LVSWWDA) to ensure the project starts immediately, adding that plans are underway to establish a sewerage system in Nyamira Town, with feasibility studies expected to guide its implementation.
                        </p>

                        <!-- Bill Settlement Announcement -->
                        <div class="my-8 p-6 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">Outstanding Bills Settlement</p>
                            <p class="text-gray-700 leading-relaxed">
                                Governor Nyaribo further revealed that the County Government has begun settling outstanding bills owed by NYAWASCO, including an initial payment of approximately KSh 20 million.
                            </p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            He also added that a technical team led by a Managing Director will soon be recruited to strengthen the utility's operations and ensure long-term sustainability.
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            The Governor also confirmed that the County will provide adequate land for the solarization project, which is expected to commence immediately once final cost estimates—projected at between KSh 300 million and KSh 400 million—are confirmed.
                        </p>

                        <!-- LVSWWDA CEO Statement -->
                        <div class="my-8 p-6 bg-purple-50 border-l-4 border-purple-500 rounded-r-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">LVSWWDA CEO Statement</p>
                            <p class="text-gray-700 leading-relaxed">
                                LVSWWDA Chief Executive Officer, Jackine Kemunto Momanyi noted that although the Nyamira water Supply System was completed several years ago, it has never been officially commissioned.
                            </p>
                            <p class="text-gray-700 leading-relaxed mt-3">
                                "We are here to assess the current state of the utility and prepare it for commissioning, especially ahead of the President's visit," she said.
                            </p>
                        </div>

                        <!-- Tender Information -->
                        <div class="my-8 p-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">KSh 169 Million Tender Awarded</p>
                            <p class="text-gray-700 leading-relaxed">
                                Momanyi disclosed that the agency has already awarded a KSh 169 million tender for rehabilitation and last-mile connectivity, aimed at improving water access across Nyamira Town. The works will include addressing siltation challenges and repairing non-functional pumps.
                            </p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            She highlighted high electricity costs and frequent power outages as major challenges affecting water supply, noting that solarization will significantly reduce operational expenses and ensure a reliable water supply.
                        </p>

                        <!-- Solarization Funding -->
                        <div class="my-8 p-6 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-solar-panel text-green-600 text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-gray-800 mb-2">Solarization Funding</p>
                                    <p class="text-gray-700 leading-relaxed">
                                        An initial allocation of <span class="font-bold">KSh 150 million</span> is expected to start off the solarization.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Sewerage System Plans -->
                        <div class="my-8 p-6 bg-blue-50 rounded-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-2">Sewerage System Development</p>
                            <p class="text-gray-700 leading-relaxed">
                                Additionally, Momanyi pointed out that Nyamira remains one of the few counties without a sewerage system, revealing that proposals have already been submitted to potential financiers and it will be finalized once funding is accessed.
                            </p>
                        </div>

                        <p class="text-gray-700 leading-relaxed mb-6">
                            The meeting also focused on enhancing collaboration between LVSWWDA, the County Government, and NYAWASCO to improve service delivery.
                        </p>

                        <!-- Project Impact -->
                        <div class="my-10 p-6 bg-green-50 rounded-lg">
                            <p class="text-lg font-semibold text-gray-800 mb-3">Expected Impact</p>
                            <p class="text-gray-700 leading-relaxed">
                                Once completed, the combined efforts in rehabilitation, solarization, and last-mile connectivity are expected to transform water service delivery in Nyamira County and ensure a sustainable supply for residents.
                            </p>
                        </div>

                        <!-- Attendees Section -->
                        <div class="my-10">
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Attended by:</h4>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h5 class="font-semibold text-gray-900 mb-2">County Officials:</h5>
                                        <ul class="text-gray-600 text-sm space-y-1">
                                            <li>• Dr. Jack Magara (County Secretary)</li>
                                            <li>• Alice Manoti (CECM Water)</li>
                                            <li>• Stephen Oboso (CECM Finance)</li>
                                            <li>• Erastus Orina (County Attorney)</li>
                                            <li>• Richard Onyinkwa (Chief Officer - Water)</li>
                                            <li>• Josphat Gori (Chief Officer - Lands)</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-900 mb-2">NYAWASCO & Others:</h5>
                                        <ul class="text-gray-600 text-sm space-y-1">
                                            <li>• Eng. Dr. Lugard Ogaro (NYAWASCO Board Chair)</li>
                                            <li>• NYAWASCO Board Members</li>
                                            <li>• LVSWWDA Representatives</li>
                                            <li>• Other County Officials</li>
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
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-solar-panel text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Solarization Project</h5>
                                        <p class="text-gray-600 text-sm">KSh 150M initial allocation for solar energy</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-hand-holding-usd text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Bills Settlement</h5>
                                        <p class="text-gray-600 text-sm">KSh 20M initial payment to clear outstanding bills</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tint text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Last-Mile Connectivity</h5>
                                        <p class="text-gray-600 text-sm">KSh 169M tender for rehabilitation and connections</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-city text-yellow-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="font-semibold text-gray-900 mb-1">Sewerage System</h5>
                                        <p class="text-gray-600 text-sm">Plans underway for Nyamira Town sewerage system</p>
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
                        <a href="{{ route('publication.show', 'nyamira-unveils-nyawasco-board') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">NYAWASCO Board Inaugurated</h4>
                            <p class="text-sm text-gray-600">8th January, 2026</p>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">Annual Report 2025</h4>
                            <p class="text-sm text-gray-600">Coming March 2025</p>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                            <h4 class="font-semibold text-gray-900 mb-1">Strategic Plan 2025-2030</h4>
                            <p class="text-sm text-gray-600">Coming February 2025</p>
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
                            <span class="font-semibold">19th March, 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-semibold">Infrastructure Development</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reading Time:</span>
                            <span class="font-semibold">4 minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Word Count:</span>
                            <span class="font-semibold">520 words</span>
                        </div>
                    </div>
                </div>

                <!-- Key Numbers -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Funding</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-700">KSh 150M</div>
                            <div class="text-sm text-gray-600">Initial Solarization Allocation</div>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-700">KSh 169M</div>
                            <div class="text-sm text-gray-600">Rehabilitation & Last-Mile Tender</div>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-700">KSh 300-400M</div>
                            <div class="text-sm text-gray-600">Total Solarization Project Cost</div>
                        </div>
                    </div>
                </div>

                <!-- Media Contact -->
                <div class="bg-gradient-to-b from-green-600 to-green-700 rounded-xl p-6 text-white">
                    <h3 class="text-lg font-semibold mb-3">Media Contact</h3>
                    <p class="text-green-100 text-sm mb-4">
                        For media inquiries about this publication
                    </p>
                    <a href="mailto:media@nyawasco.co.ke" class="inline-flex items-center justify-center w-full px-4 py-2 bg-white text-green-700 rounded-lg font-semibold hover:bg-green-50 transition text-sm">
                        <i class="fas fa-envelope mr-2"></i>
                        Email Media Desk
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Publications -->
    <div class="text-center">
        <a href="{{ route('publications') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Publications
        </a>
    </div>

</main>
@endsection
