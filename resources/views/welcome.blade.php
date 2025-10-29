@extends('layouts.app')

@section('title', $title)

@section('content')


    <!-- Page Content Wrapper -->
    <div class="pt-20">
        {{-- Main content goes here --}}
    </div>

    <!-- Live Pulse Widget -->
    <live-pulse-widget></live-pulse-widget>

    <!-- Hero Section -->
    <homepage-hero
        :user-logged-in="{{ auth()->check() ? 'true' : 'false' }}"
        user-name="{{ auth()->user()->name ?? '' }}"></homepage-hero>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

           <!-- Hero Slideshow Section -->
            <section class="mb-16 -mx-4 sm:-mx-6 lg:-mx-8 mt-2">
                <div wire:ignore class="swiper hero-swiper h-80 md:h-[450px] lg:h-[550px] w-screen">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
                                alt="Live Events"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Discover Amazing Events</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Find and book tickets for the hottest events in your city</p>
                                    <a href="{{ route('events.index') }}"
                                        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                        Explore Events
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="/img/Javent.png" 
                                alt="Create Events" 
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Host Your Event</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Reach thousands of attendees with our powerful platform</p>
                                    <button @click="signupOpen = true" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105">
                                        Get Started
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="swiper-slide relative">
                            <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                            <img src="/img/ja.png"
                                alt="Voting Contests"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Join Live Voting</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">Participate in exciting contests and shape the outcomes</p>
                                    <a href="{{ route('voting.index') }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                        Vote Now
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="swiper-slide relative">

                            <div class="absolute inset-0 bg-cover bg-center scale-110 blur-sm"
                                style="background-image: url('/img/dance.jpeg')"></div>
                            <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
                            <img src="/img/dance.jpeg"
                                alt="Book Tickets"
                                class="w-full h-full object-contain opacity-50 z-20 relative">
                            <div class="absolute inset-0 z-30 flex items-center justify-center text-center px-4">
                                <div class="text-white max-w-4xl">
                                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 text-white drop-shadow-2xl">Book Your Tickets</h2>
                                    <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 text-white font-medium drop-shadow-lg">Secure your spot at amazing events with easy, fast booking</p>
                                    <a href="{{ route('events.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white px-8 py-3 md:px-10 md:py-4 rounded-full font-bold text-lg md:text-xl transition duration-300 transform hover:scale-105 inline-block shadow-2xl">
                                        Book Your Tickets
                                    </a>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination !bottom-4"></div>

                   <!-- Navigation Buttons - Properly Styled -->
                    <div class="swiper-button-prev !w-4!h-4 md:!w-12 md:!h-12 !left-1 md:!left-2 bg-opacity-50 rounded-full shadow-lg hover:bg-purple hover:scale-90 transition-all duration-300"></div>
                    <div class="swiper-button-next !w-4 !h-4 md:!w-12 md:!h-12 !right-1 md:!right-2 bg-opacity-50 rounded-full shadow-lg hover:bg-purple hover:scale-90 transition-all duration-300"></div>
                </div>
            </section>

        <!-- Intelligent Feed Section -->
        <section class="mb-16">


            <!-- Masonry Grid Layout -->
            <div class="masonry-grid">

                <!-- Events from Livewire -->
                <livewire:event-feed />

                <!-- Voting Contests from Livewire -->
                <livewire:voting-feed />
            </div>
        </section>

        <!-- Dual-Path How It Works Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-12">How Javent Works</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <!-- For Attendees Column -->
                <div class="bg-black rounded-2xl shadow-lg p-8 ticket-gradient text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-pink-400">For Attendees & Voters</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">1</span>
                            <div>
                                <h4 class="font-semibold">Discover Amazing Events</h4>
                                <p class="text-white text-opacity-90 text-sm">Browse through curated events and voting contests in your area</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">2</span>
                            <div>
                                <h4 class="font-semibold">Book Tickets or Cast Votes</h4>
                                <p class="text-white text-opacity-90 text-sm">Secure your spot with easy booking or participate in live voting</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">3</span>
                            <div>
                                <h4 class="font-semibold">Enjoy & Engage</h4>
                                <p class="text-white text-opacity-90 text-sm">Attend unforgettable events and see your votes shape outcomes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- For Organizers Column -->
                <div class="bg-black rounded-2xl shadow-lg p-8 vote-gradient text-white">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-pink-400">For Organizers</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">1</span>
                            <div>
                                <h4 class="font-semibold">Create Your Event</h4>
                                <p class="text-white text-opacity-90 text-sm">Set up events with ticketing, seating, and voting options</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">2</span>
                            <div>
                                <h4 class="font-semibold">Manage & Promote</h4>
                                <p class="text-white text-opacity-90 text-sm">Use our tools to manage attendees and promote your event</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-white text-pink-400 bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-4 mt-1">3</span>
                            <div>
                                <h4 class="font-semibold">Analyze & Grow</h4>
                                <p class="text-white text-opacity-90 text-sm">Get insights from analytics to improve your future events</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-12">What Our Community Says</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            KO
                        </div>
                        <div>
                            <h4 class="font-semibold text-black">Kephas Ouma.</h4>
                            <p class="text-gray-600 text-sm">CEO, HeartBeat of Style</p>
                        </div>
                    </div>
                    <p class="text-gray-700">“Javent made our event feel truly interactive through ticket bookings and more. The live voting kept our audience energized, and the sleek poster designs gave our brand a polished, professional edge.”</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-pink-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            GS
                        </div>
                        <div>
                            <h4 class="font-semibold text-black">Robin Sansiro.</h4>
                            <p class="text-gray-600 text-sm">CEO, The Giftland</p>
                        </div>
                    </div>
                    <p class="text-gray-700">“Javent’s ticketing system made our event planning seamless. From branded digital tickets to smooth check-ins, The Giftland delivered a premium experience our guests won’t forget.”</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                            SB
                        </div>
                        <div>
                            <h4 class="font-semibold text-black">Solomon Batasi.</h4>
                            <p class="text-gray-600 text-sm">Manager, JavaPA LIMITED</p>
                        </div>
                    </div>
                    <p class="text-gray-700">“With Javent, we never worry about security. Our customers trust the site, and the payment process is smooth, encrypted, and completely hassle-free.”</p>
                </div>
            </div>
        </section>
        <!-- Partners Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-center text-purple-500 mb-8">Our Trusted Partners</h2>

            <!-- Partner Description -->
            <div class="text-center mb-8">
                <p class="text-gray-300 text-lg max-w-3xl mx-auto leading-relaxed">
                    Collaborating with industry leaders to deliver exceptional event and voting experiences
                    through innovative technology and seamless partnerships.
                </p>
            </div>

            <!-- Partners Logos Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-4xl mx-auto">
                <!-- Partner 1 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35 cursor-pointer"
                    @click="openPartnerModal(
                        'HeartBeat of Style', 
                        '/img/LHTBT.png', 
                        'Heartbeat of Style is a creative hub that empowers youth through fashion, modeling and community impact programs. We provide a platform for models, designers and creatives to showcase talent, grow professionally and build sustainable careers.',
                        {
                            facebook: 'https://www.facebook.com/share/1BhY6sdBJk/?mibextid=wwXIfr',
                            instagram: 'https://www.instagram.com/heartbeat_of_style?igsh=MWdmZWhjeTR4NWRrZA%3D%3D&utm_source=qr',
            
                            tiktok: 'https://www.tiktok.com/@heartbeat_of_style?_t=ZM-90vZzBM3bJs&_r=1'
                        }
                    )">
                    <div class="w-full h-23 flex items-center justify-center mb-2">
                        <img src="/img/LHTBT.png"
                            alt="HeartBeat of Style"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">HeartBeat of Style</h3>
                </div>

                <!-- Partner 2 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35 cursor-pointer"
                    @click="openPartnerModal(
                        'JavaPA Limited', 
                        '/img/java.png', 
                        'JavaPA Limited is a technology solutions provider specializing in event management software and digital platforms. They bring cutting-edge technology to streamline event operations and enhance attendee experiences through innovative digital solutions.',
                        {
                            facebook: 'https://web.facebook.com/people/Javapa-Limited/61579385817243/',
                            instagram: 'https://www.instagram.com/jav.apa/',
                            twitter: 'https://twitter.com/search?q=%23JavaPa&vertical=default',
                            linkedin: 'https://ke.linkedin.com/company/javapa'
                        }
                    )">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/java.png"
                            alt="JavaPA"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">JavaPA Limited</h3>
                </div>

                <!-- Partner 3 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35 cursor-pointer"
                    @click="openPartnerModal(
                        'Giftland Events Limited', 
                        '/img/gifte.png', 
                        'Giftland Events Limited is a full-service event management company specializing in corporate events, weddings, and large-scale celebrations. With years of experience, they bring professionalism and creativity to every event they organize.',
                        {
                            facebook: 'https://facebook.com/giftlandevents',
                            instagram: 'https://instagram.com/giftlandevents',
                            twitter: 'https://twitter.com/giftlandevents',
                            linkedin: 'https://linkedin.com/company/giftlandevents'
                        }
                    )">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/gifte.png"
                            alt="The Giftland Events"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">Giftland Events Limited</h3>
                </div>

                <!-- Partner 4 -->
                <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col items-center justify-center hover:shadow-md transition-all duration-300 hover:scale-105 border border-gray-200 h-35 cursor-pointer"
                    @click="openPartnerModal(
                        'The Giftland Studios and Restaurant', 
                        '/img/land.png', 
                        'The Giftland Studios and Restaurant offers premium event venues and catering services. Their state-of-the-art studios and restaurant facilities provide the perfect backdrop for memorable events, from intimate gatherings to large celebrations.',
                        {
                            facebook: 'https://facebook.com/giftlandstudios',
                            instagram: 'https://instagram.com/giftlandstudios',
                            twitter: 'https://twitter.com/giftlandstudios',
                            linkedin: 'https://linkedin.com/company/giftlandstudios'
                        }
                    )">
                    <div class="w-full h-23 bg-transparent flex items-center justify-center mb-2">
                        <img src="/img/land.png"
                            alt="The Giftland Studios and Restaurant"
                            class="w-full h-full object-contain">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-xs text-center leading-tight mt-1">The Giftland Studios and Restaurant</h3>
                </div>
            </div>
        </section>

        <!-- Partner Modal -->
        <div x-show="partnerModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4 transition-opacity duration-300">
            <div class="bg-black rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative transform transition-all duration-300 scale-100"
                @click.away="partnerModalOpen = false">
                <!-- Close Button -->
                <button @click="partnerModalOpen = false" 
                        class="absolute top-4 right-4 z-10 text-gray-500 hover:text-gray-700 transition-colors bg-white rounded-full p-2 shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Content -->
                <div class="p-6">
                    <!-- Partner Image -->
                    <div class="flex justify-center mb-6">
                        <img x-bind:src="currentPartner.image" 
                            x-bind:alt="currentPartner.name"
                            class="h-32 w-auto object-contain rounded-lg">
                    </div>

                    <!-- Partner Name -->
                    <h2 x-text="currentPartner.name" class="text-2xl md:text-3xl font-bold text-center text-purple-600 mb-4"></h2>

                    <!-- Partner Description -->
                    <div class="prose max-w-none">
                        <p x-text="currentPartner.description" class="text-white text-lg leading-relaxed text-center"></p>
                    </div>

                    <!-- Social Links -->
                    <div class="mt-6 text-center" x-show="currentPartner.socials">
                        <h4 class="font-semibold mb-4 text-pink-400">Connect</h4>
                        <div class="flex space-x-4 justify-center">
                            <!-- Facebook -->
                            <a x-bind:href="currentPartner.socials.facebook" 
                            x-show="currentPartner.socials.facebook"
                            target="_blank"
                            class="text-gray-400 hover:text-pink-300 transition-colors">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- Twitter/X -->
                            <a x-bind:href="currentPartner.socials.twitter" 
                            x-show="currentPartner.socials.twitter"
                            target="_blank"
                            class="text-gray-400 hover:text-pink-300 transition-colors">
                                <span class="sr-only">X</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.39 3.61h2.56l-7.42 8.48 8.72 10.3h-6.87l-5.39-6.36-6.16 6.36H2.4l7.9-8.15L1.6 3.61h7.05l4.8 5.67 6.94-5.67z"/>
                                </svg>
                            </a>

                            <!-- LinkedIn -->
                            <a x-bind:href="currentPartner.socials.linkedin" 
                            x-show="currentPartner.socials.linkedin"
                            target="_blank"
                            class="text-gray-400 hover:text-pink-300 transition-colors">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8h4V24h-4V8zm7.5 0h3.8v2.2h.05c.53-1 1.83-2.2 3.75-2.2 4 0 4.75 2.63 4.75 6.05V24h-4v-7.5c0-1.8-.03-4.1-2.5-4.1-2.5 0-2.88 1.95-2.88 3.97V24h-4V8z"/>
                                </svg>
                            </a>

                            <!-- Instagram -->
                            <a x-bind:href="currentPartner.socials.instagram" 
                            x-show="currentPartner.socials.instagram"
                            target="_blank"
                            class="text-gray-400 hover:text-pink-300 transition-colors">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>
                                </svg>
                            </a>

                            <!-- TikTok -->
                            <a x-bind:href="currentPartner.socials.tiktok" 
                            x-show="currentPartner.socials.tiktok"
                            target="_blank"
                            class="text-gray-400 hover:text-pink-300 transition-colors">
                                <span class="sr-only">TikTok</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('partners', () => ({
                partnerModalOpen: false,
                currentPartner: {
                    name: '',
                    image: '',
                    description: '',
                    socials: null
                },
                
                openPartnerModal(name, image, description, socials = null) {
                    this.currentPartner = {
                        name: name,
                        image: image,
                        description: description,
                        socials: socials
                    };
                    this.partnerModalOpen = true;
                }
            }));
        });
        </script>
    </main>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Script for masonry grid filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const gridItems = document.querySelectorAll('.grid-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');

                    // Filter items
                    gridItems.forEach(item => {
                        if (filter === 'all') {
                            item.style.display = 'block';
                        } else {
                            if (item.classList.contains(filter)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });

            new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.hero-swiper .swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: '.hero-swiper .swiper-button-next',
                    prevEl: '.hero-swiper .swiper-button-prev',
                },
            });
        });
    </script>

@endsection
