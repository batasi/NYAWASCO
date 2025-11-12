@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

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

  <!-- Hero / Welcome Section -->
  <section class="mb-16 -mx-4 sm:-mx-6 lg:-mx-8 mt-2">
    <div class="swiper hero-swiper h-80 md:h-[450px] lg:h-[550px] w-screen">
      <div class="swiper-wrapper">

        <!-- Slide 1 -->
        <div class="swiper-slide relative">
          <div class="absolute inset-0 bg-blue-900 bg-opacity-50 z-10"></div>
          <img src="/img/water-hero-1.jpg" alt="Smart Water Billing System" class="w-full h-full object-cover">
          <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
            <div class="text-white max-w-4xl">
              <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Welcome to AquaBill</h2>
              <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                Efficient, transparent water billing &amp; management for utilities and customers.
              </p>
              <a href="{{ route('login') }}"
                 class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                Login to Portal
              </a>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="swiper-slide relative">
          <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
          <img src="/img/water-hero-2.jpg" alt="Real-time Meter Monitoring" class="w-full h-full object-cover">
          <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
            <div class="text-white max-w-4xl">
              <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Real-Time Usage Monitoring</h2>
              <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                Connect smart meters, track usage live and let customers view consumption online.
              </p>
              <a href="#features"
                 class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                See Features
              </a>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="swiper-slide relative">
          <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
          <img src="/img/water-hero-3.jpg" alt="Automated Billing & Payments" class="w-full h-full object-cover">
          <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
            <div class="text-white max-w-4xl">
              <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Automated Billing & Instant Payments</h2>
              <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                Generate invoices automatically and enable customers to pay via mobile, bank or web.
              </p>
              <a href="{{ route('contact') }}"
                 class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 md:px-8 md:py-3 rounded-full font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                Contact Us
              </a>
            </div>
          </div>
        </div>

      </div>

      <div class="swiper-pagination !bottom-4"></div>
      <div class="swiper-button-prev !w-4 !h-4 md:!w-12 md:!h-12 !left-1 md:!left-2 bg-opacity-50 rounded-full shadow-lg"></div>
      <div class="swiper-button-next !w-4 !h-4 md:!w-12 md:!h-12 !right-1 md:!right-2 bg-opacity-50 rounded-full shadow-lg"></div>
    </div>
  </section>

  <!-- Quick Links Section (Services) -->
  <section id="services" class="mb-16 py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 text-center mb-8">Our Services</h2>
      <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-8">
        <a href="#" class="bg-white p-6 rounded-2xl shadow-hover transition duration-300 hover:shadow-xl text-center">
          <img src="/img/icon-new-water.png" alt="New Water Connection" class="mx-auto mb-4 w-16 h-16">
          <h3 class="text-lg font-semibold mb-2">New Water Connection</h3>
          <p class="text-sm text-gray-600">Apply online for a new water connection quickly and seamlessly.</p>
        </a>
        <a href="#" class="bg-white p-6 rounded-2xl shadow-hover transition duration-300 hover:shadow-xl text-center">
          <img src="/img/icon-billing.png" alt="View/Pay Bills" class="mx-auto mb-4 w-16 h-16">
          <h3 class="text-lg font-semibold mb-2">View &amp; Pay Bills</h3>
          <p class="text-sm text-gray-600">Customers can view and settle bills via mobile, web or bank.</p>
        </a>
        <a href="#" class="bg-white p-6 rounded-2xl shadow-hover transition duration-300 hover:shadow-xl text-center">
          <img src="/img/icon-meter-reading.png" alt="Meter Reading" class="mx-auto mb-4 w-16 h-16">
          <h3 class="text-lg font-semibold mb-2">Meter Reading</h3>
          <p class="text-sm text-gray-600">Upload or view your meter readings and track usage over time.</p>
        </a>
        <a href="#" class="bg-white p-6 rounded-2xl shadow-hover transition duration-300 hover:shadow-xl text-center">
          <img src="/img/icon-support.png" alt="Customer Support" class="mx-auto mb-4 w-16 h-16">
          <h3 class="text-lg font-semibold mb-2">Customer Support</h3>
          <p class="text-sm text-gray-600">Submit enquiries, lodge complaints or report issues.</p>
        </a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="mb-16 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-8">About AquaBill</h2>
      <p class="text-lg md:text-xl text-gray-700 max-w-4xl mx-auto mb-12 leading-relaxed">
        AquaBill is a comprehensive water billing and management platform designed for utilities.
        From smart meter integration, through to invoice generation, online payments and reporting — we help you manage every drop efficiently.
      </p>
      <div class="grid sm:grid-cols-2 gap-8 mt-10">
        <div>
          <h3 class="text-2xl font-semibold text-blue-600 mb-4">Our Mission</h3>
          <p class="text-gray-700">To provide transparent, efficient and customer-centric water billing services for utilities everywhere.</p>
        </div>
        <div>
          <h3 class="text-2xl font-semibold text-blue-600 mb-4">Our Vision</h3>
          <p class="text-gray-700">A future where every utility has real-time data, zero billing disputes and empowered customers.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Metrics / Quick Stats Section -->
  <section class="mb-16 bg-blue-50 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-8">Key Performance Metrics</h2>
      <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-2xl shadow-hover transition duration-300">
          <h3 class="text-5xl font-bold text-cyan-600 mb-2">99%</h3>
          <p class="text-lg text-gray-700">Water Coverage</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-hover transition duration-300">
          <h3 class="text-5xl font-bold text-cyan-600 mb-2">100%</h3>
          <p class="text-lg text-gray-700">Metering Coverage</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-hover transition duration-300">
          <h3 class="text-5xl font-bold text-cyan-600 mb-2">16.8%</h3>
          <p class="text-lg text-gray-700">Non-Revenue Water</p>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-hover transition duration-300">
          <h3 class="text-5xl font-bold text-cyan-600 mb-2">24 hrs</h3>
          <p class="text-lg text-gray-700">Service Hours</p>
        </div>
      </div>
    </div>
  </section>

  <!-- News & Events Section -->
  <section id="news" class="mb-16 py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-12">Latest News & Events</h2>
      <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Repeat this card for each article -->
        <div class="group bg-gray-50 p-6 rounded-2xl shadow-md hover:shadow-lg transition duration-300 text-left">
          <img src="/img/news-1.jpg" alt="News item 1" class="w-full h-40 object-cover rounded-lg mb-4">
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Tree Growing Milestone: 2,000+ Indigenous Trees Planted</h3>
          <p class="text-sm text-gray-600">November 5, 2025</p>
          <a href="#"
             class="text-cyan-600 hover:text-cyan-800 font-semibold mt-4 inline-block">Read More →</a>
        </div>
        <!-- ... two more cards ... -->
      </div>
      <div class="mt-12">
        <a href="#" class="text-blue-700 font-semibold hover:underline">View All News & Events</a>
      </div>
    </div>
  </section>

  <!-- Partners / Certifications Section -->
  <section class="mb-16 py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-8">Our Integrations & Partners</h2>
      <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
        <img src="/img/partner-mpesa.png" alt="MPESA Integration" class="w-full h-20 object-contain grayscale hover:grayscale-0 transition duration-300">
        <img src="/img/partner-bank.png" alt="Bank API" class="w-full h-20 object-contain grayscale hover:grayscale-0 transition duration-300">
        <img src="/img/partner-smart-meter.png" alt="Smart Meter Vendor" class="w-full h-20 object-contain grayscale hover:grayscale-0 transition duration-300">
        <img src="/img/partner-regulator.png" alt="Regulator Certification" class="w-full h-20 object-contain grayscale hover:grayscale-0 transition duration-300">
      </div>
    </div>
  </section>

  <!-- Contact / Map Section -->
  <section id="contact" class="mb-16 py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-8">Get in Touch</h2>
      <p class="text-lg md:text-xl text-gray-700 max-w-4xl mx-auto mb-12 leading-relaxed">
        Have questions or need support? Reach out to us via phone, email or visit our office.
      </p>
      <div class="grid md:grid-cols-2 gap-12">
        <!-- Map -->
        <div class="w-full h-64">
          <iframe
            src="https://www.google.com/maps/embed?pb=…"
            width="100%" height="100%" class="border-0 rounded-lg" allowfullscreen="" loading="lazy"
          ></iframe>
        </div>
        <!-- Contact Form -->
        <div>
          <form action="#" method="POST" class="bg-white p-8 rounded-2xl shadow-lg border border-blue-100 space-y-4">
            @csrf
            <div>
              <label for="name" class="block text-gray-700 font-semibold mb-1">Full Name</label>
              <input type="text" name="name" id="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none">
            </div>
            <div>
              <label for="email" class="block text-gray-700 font-semibold mb-1">Email</label>
              <input type="email" name="email" id="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none">
            </div>
            <div>
              <label for="message" class="block text-gray-700 font-semibold mb-1">Message</label>
              <textarea name="message" id="message" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"></textarea>
            </div>
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-2 rounded-full transition duration-300 transform hover:scale-105">
              Send Message
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

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
