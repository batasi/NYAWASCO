@extends('layouts.app')

@section('title', 'NYAWASCO - Water and Sanitation Services')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

    <!-- Hero Section with Slideshow -->
    <section class="relative">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-blue-900 bg-opacity-50 z-10"></div>
                    <img src="/img/water-hero-1.jpg" alt="NYAWASCO Water Services" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Welcome to NYAWASCO</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                Providing reliable water and sanitation services to Nyamira County
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('login') }}"
                                   class="bg-white text-blue-700 hover:bg-blue-50 px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                    Customer Portal
                                </a>
                                <a href="#services"
                                   class="border-2 border-white text-white hover:bg-white hover:text-blue-700 px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                    Our Services
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                    <img src="/img/water-hero-2.jpg" alt="Water Conservation" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Clean Water for All</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                Committed to sustainable water management and environmental conservation
                            </p>
                            <a href="#about"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                    <img src="/img/water-hero-3.jpg" alt="Community Service" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">24/7 Customer Support</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                Your trusted partner in water and sanitation services
                            </p>
                            <a href="#contact"
                               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-pagination !bottom-4"></div>
            <div class="swiper-button-prev !w-10 !h-10 md:!w-12 md:!h-12 !left-2 bg-white bg-opacity-20 rounded-full shadow-lg"></div>
            <div class="swiper-button-next !w-10 !h-10 md:!w-12 md:!h-12 !right-2 bg-white bg-opacity-20 rounded-full shadow-lg"></div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="quick-links-section py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Quick Access</h2>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                Easy access to our most popular services and resources
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 md:gap-6">
            <!-- Water Connection -->
            <a href="{{ route('water-connection') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-water">
                    <i class="fas fa-faucet"></i>
                </div>
                <div class="quick-link-text">New Water Connection</div>
            </a>
            
            <!-- Sewer Connection -->
            <a href="{{ route('sewer-connection') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-sewer">
                    <i class="fas fa-recycle"></i>
                </div>
                <div class="quick-link-text">New Sewer Connection</div>
            </a>
            
            <!-- Bill Payment -->
            <a href="{{ route('bill-payment') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-payment">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="quick-link-text">Pay For Services</div>
            </a>
            
            <!-- Complaints -->
            <a href="{{ route('complaints.create') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-complaint">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="quick-link-text">Lodge A Complaint</div>
            </a>
            
            <!-- Corruption Report -->
            <a href="{{ route('corruption-report') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-corruption">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="quick-link-text">Report Corruption</div>
            </a>
            
            <!-- Tenders -->
            <a href="{{ route('tenders') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-tenders">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="quick-link-text">Tenders</div>
            </a>
            
            <!-- Careers -->
            <a href="{{ route('careers') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-careers">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="quick-link-text">Careers</div>
            </a>
            
            <!-- Reports -->
            <a href="{{ route('reports') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-reports">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="quick-link-text">Reports</div>
            </a>
            
            <!-- Downloads -->
            <a href="{{ route('downloads') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-downloads">
                    <i class="fas fa-download"></i>
                </div>
                <div class="quick-link-text">Publications & Downloads</div>
            </a>
            
            <!-- News -->
            <a href="{{ route('news') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-news">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="quick-link-text">News & Events</div>
            </a>
            
            <!-- Contacts -->
            <a href="#contact" class="quick-link-card group">
                <div class="quick-link-icon icon-contacts">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="quick-link-text">Contacts</div>
            </a>
            
            <!-- Documentary -->
            <a href="{{ route('documentary') }}" class="quick-link-card group">
                <div class="quick-link-icon icon-documentary">
                    <i class="fas fa-video"></i>
                </div>
                <div class="quick-link-text">Nyawasco Documentary</div>
            </a>
        </div>
    </div>
</section>

    <!-- Quick Stats Section -->
    <section class="py-8 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">50,000+</div>
                    <div class="text-sm md:text-base text-gray-600">Customers Served</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">99%</div>
                    <div class="text-sm md:text-base text-gray-600">Water Coverage</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">24/7</div>
                    <div class="text-sm md:text-base text-gray-600">Service Availability</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">15+</div>
                    <div class="text-sm md:text-base text-gray-600">Years Experience</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Our Services</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Comprehensive water and sanitation solutions for residential, commercial, and industrial customers
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <!-- Service 1 -->
                <div class="service-card p-6 rounded-lg text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-faucet text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Water Supply</h3>
                    <p class="text-gray-600 text-sm">
                        Reliable and safe water supply for domestic, commercial, and industrial use
                    </p>
                    <a href="{{ route('water-supply') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Learn More →
                    </a>
                </div>

                <!-- Service 2 -->
                <div class="service-card p-6 rounded-lg text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-recycle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Sewerage Services</h3>
                    <p class="text-gray-600 text-sm">
                        Efficient wastewater management and sanitation services for a cleaner environment
                    </p>
                    <a href="{{ route('sewerage') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Learn More →
                    </a>
                </div>

                <!-- Service 3 -->
                <div class="service-card p-6 rounded-lg text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-plug text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">New Connections</h3>
                    <p class="text-gray-600 text-sm">
                        Easy and efficient process for new water and sewerage connections
                    </p>
                    <a href="{{ route('new-connections') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Apply Now →
                    </a>
                </div>

                <!-- Service 4 -->
                <div class="service-card p-6 rounded-lg text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-credit-card text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Bill Payments</h3>
                    <p class="text-gray-600 text-sm">
                        Convenient payment options including mobile money, bank, and online payments
                    </p>
                    <a href="{{ route('payments') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Pay Now →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Online Services Section -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Online Services</h2>
                <p class="text-lg opacity-90 max-w-3xl mx-auto">
                    Access our services conveniently from anywhere, anytime
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">New Connection</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Apply for a new water connection online
                    </p>
                    <a href="{{ route('water-connection') }}" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Apply Online
                    </a>
                </div>

                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Pay Bill</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Pay your water bill securely online
                    </p>
                    <a href="{{ route('bill-payment') }}" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Pay Now
                    </a>
                </div>

                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-headset text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Support</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Get help and report issues
                    </p>
                    <a href="{{ route('support') }}" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Get Help
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-6">About NYAWASCO</h2>
                    <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                        Nyamira Water and Sanitation Company (NYAWASCO) is committed to providing reliable, 
                        affordable, and sustainable water and sanitation services to the residents of Nyamira County.
                    </p>
                    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                        With over 15 years of experience in water service provision, we continuously strive 
                        to improve our infrastructure and services to meet the growing demands of our community.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="stat-card p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-700 mb-1">Vision</div>
                            <p class="text-sm text-gray-600">To be the leading water service provider in Kenya</p>
                        </div>
                        <div class="stat-card p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-700 mb-1">Mission</div>
                            <p class="text-sm text-gray-600">Provide quality water and sanitation services sustainably</p>
                        </div>
                    </div>

                    <a href="{{ route('about') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block">
                        Read More About Us
                    </a>
                </div>
                
                <div class="relative">
                    <img src="/img/about-water.jpg" alt="About NYAWASCO" class="rounded-lg shadow-lg w-full">
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-lg shadow-lg">
                        <div class="text-3xl font-bold text-blue-700 mb-2">15+ Years</div>
                        <div class="text-gray-600">Of Service Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Ongoing Projects</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Investing in infrastructure development to improve service delivery
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Project 1 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/project-1.jpg" alt="Water Network Expansion" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Water Network Expansion</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Extending water supply to underserved areas in Nyamira County
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Ongoing</span>
                            <span>Completion: Dec 2024</span>
                        </div>
                    </div>
                </div>

                <!-- Project 2 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/project-2.jpg" alt="Sewerage System Upgrade" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Sewerage System Upgrade</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Modernizing wastewater treatment facilities for better efficiency
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Ongoing</span>
                            <span>Completion: Mar 2025</span>
                        </div>
                    </div>
                </div>

                <!-- Project 3 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/project-3.jpg" alt="Digital Metering" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Smart Metering Project</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Installing digital meters for accurate billing and consumption monitoring
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Planning Phase</span>
                            <span>Start: Jan 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Updates Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">News & Updates</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Stay informed with the latest news and announcements from NYAWASCO
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- News 1 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/news-1.jpg" alt="Water Conservation" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">November 15, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Water Conservation Campaign Launch</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            NYAWASCO launches a new water conservation awareness campaign to promote sustainable water usage...
                        </p>
                        <a href="{{ route('news.show', 'water-conservation-campaign') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>

                <!-- News 2 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/news-2.jpg" alt="Infrastructure" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 28, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">New Water Treatment Plant</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Construction begins on new state-of-the-art water treatment plant to increase capacity...
                        </p>
                        <a href="{{ route('news.show', 'new-water-treatment-plant') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>

                <!-- News 3 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/news-3.jpg" alt="Community" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 12, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Community Outreach Program</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            NYAWASCO engages with local communities to address water access challenges...
                        </p>
                        <a href="{{ route('news.show', 'community-outreach-program') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('news') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block">
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Contact Us</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Get in touch with us for inquiries, support, or feedback
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-2xl font-semibold text-blue-700 mb-6">Get In Touch</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Our Address</h4>
                                <p class="text-gray-600">NYAWASCO Headquarters<br>Nyamira, Kenya</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Phone Numbers</h4>
                                <p class="text-gray-600">
                                    +254 728 725 559<br>
                                    +254 714 725 559
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email Address</h4>
                                <p class="text-gray-600">info@nyawasco.co.ke</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Working Hours</h4>
                                <p class="text-gray-600">
                                    Monday - Friday: 8:00 AM - 5:00 PM<br>
                                    Saturday: 8:00 AM - 1:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <h3 class="text-2xl font-semibold text-blue-700 mb-6">Send us a Message</h3>
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" id="email" name="email" required 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <input type="text" id="subject" name="subject" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea id="message" name="message" rows="5" required 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                        </div>
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Banner -->
    <div class="emergency-banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="font-semibold">Emergency Contact:</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-phone-alt mr-2"></i>
                    <span>+254 728 725 559 (24/7 Emergency Line)</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper for hero section
            new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
@endsection