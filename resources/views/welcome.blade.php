@extends('layouts.app')

@section('title', 'NYAWASCO - Water and Sanitation Services')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

    <!-- Hero Section -->
<section class="relative">
    <div class="swiper hero-swiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 - Welcome -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-11.jpeg" alt="NYAWASCO Water Services"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <!-- Text container with blue water theme -->
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-900/80 to-blue-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Providing Reliable</span>
                                <span class="block text-blue-100">Water & Sanitation Services</span>
                            </h1>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Trusted partner in sustainable water management for Nyamira County
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 - Quality -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-21.jpeg" alt="Water Quality"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-800/80 to-cyan-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <div class="mb-6 md:mb-8">
                                <span class="inline-block bg-white/30 text-sm font-bold px-4 py-2 rounded-full mb-4 shadow-lg">
                                    99.7% Water Quality Compliance
                                </span>
                            </div>
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Clean, Safe Water</span>
                                <span class="block text-cyan-100">For Every Community</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Committed to excellence in water treatment and distribution
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 - Innovation -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-31.jpeg" alt="Water Infrastructure"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-900/80 to-teal-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Innovating Water</span>
                                <span class="block text-teal-100">Infrastructure</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Modern solutions for sustainable water management and distribution
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4 - Additional Image -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-4.jpeg" alt="Water Services"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-800/80 to-blue-600/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Serving Nyamira</span>
                                <span class="block text-blue-100">With Excellence</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Your trusted partner in water and sanitation services
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 5 - Additional Image -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-5.jpeg" alt="Water Treatment"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-cyan-800/80 to-blue-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Advanced Treatment</span>
                                <span class="block text-cyan-100">Technology</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                State-of-the-art facilities for pure, safe water
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 6 - Additional Image -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-3.jpeg" alt="Community Service"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-700/80 to-teal-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Community First</span>
                                <span class="block text-blue-100">Approach</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Putting the needs of our community at the forefront
                            </p>
                            <a href="#contact"
                               class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                Get Connected
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 7 - Additional Image -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-2.jpeg" alt="Water Distribution"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-blue-800/80 to-cyan-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Reliable Distribution</span>
                                <span class="block text-cyan-100">Network</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Ensuring water reaches every corner of Nyamira County
                            </p>
                            <a href="#services"
                               class="border-2 border-white text-white hover:bg-white/10 px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                Our Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 8 - Additional Image -->
            <div class="swiper-slide relative">
                <img src="/img/water-hero-1.jpeg" alt="Sustainable Water"
                     class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 z-20 flex items-center justify-center px-4 sm:px-6 lg:px-8">
                    <div class="text-white max-w-4xl mx-auto text-center">
                        <div class="relative px-6 py-8 md:px-10 md:py-12 bg-gradient-to-r from-teal-800/80 to-blue-700/80 rounded-xl shadow-2xl border border-blue-300/20">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight text-shadow-lg">
                                <span class="block">Sustainable Future</span>
                                <span class="block text-teal-100">For All</span>
                            </h2>
                            <p class="text-xl md:text-2xl mb-8 md:mb-10 max-w-3xl mx-auto font-medium">
                                Building a water-secure future for generations to come
                            </p>
                            <a href="#sustainability"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2">
                                Sustainability Goals
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-8"></div>

        <!-- Navigation -->
        <div class="swiper-button-prev !left-4 !w-12 !h-12 bg-white/20 rounded-full hover:bg-white/30 transition-colors shadow-lg">
            <svg class="!w-6 !h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </div>
        <div class="swiper-button-next !right-4 !w-12 !h-12 bg-white/20 rounded-full hover:bg-white/30 transition-colors shadow-lg">
            <svg class="!w-6 !h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        speed: 800,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        autoplay: {
            delay: 8000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        pagination: {
            el: '.swiper-pagination',
            type: 'bullets',
            clickable: true,
            bulletClass: 'swiper-pagination-bullet !bg-white/70 !w-2 !h-2 !mx-1',
            bulletActiveClass: 'swiper-pagination-bullet-active !bg-white !w-8',
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        keyboard: {
            enabled: true,
        },
        on: {
            init: function() {
                document.querySelectorAll('.swiper-slide img').forEach(img => {
                    img.style.transform = 'scale(1.1)';
                    img.style.transition = 'transform 10s ease-out';
                });
            },
            slideChangeTransitionStart: function() {
                document.querySelectorAll('.swiper-slide img').forEach(img => {
                    img.style.transform = 'scale(1.1)';
                });
            },
            slideChangeTransitionEnd: function() {
                const activeSlide = this.slides[this.activeIndex];
                const activeImg = activeSlide.querySelector('img');
                if (activeImg) {
                    setTimeout(() => {
                        activeImg.style.transform = 'scale(1)';
                    }, 50);
                }
            }
        }
    });

    // Pause autoplay on hover
    const swiperContainer = document.querySelector('.hero-swiper');
    swiperContainer.addEventListener('mouseenter', () => {
        heroSwiper.autoplay.stop();
    });

    swiperContainer.addEventListener('mouseleave', () => {
        heroSwiper.autoplay.start();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            heroSwiper.slidePrev();
        } else if (e.key === 'ArrowRight') {
            heroSwiper.slideNext();
        } else if (e.key === ' ') {
            e.preventDefault();
            if (heroSwiper.autoplay.running) {
                heroSwiper.autoplay.stop();
            } else {
                heroSwiper.autoplay.start();
            }
        }
    });
});
</script>

<style>
.hero-swiper {
    --swiper-theme-color: #fff;
    --swiper-navigation-size: 24px;
    --swiper-pagination-bullet-size: 8px;
    --swiper-pagination-bullet-inactive-color: rgba(255, 255, 255, 0.7);
    --swiper-pagination-bullet-inactive-opacity: 1;
}

.swiper-button-prev:after,
.swiper-button-next:after {
    display: none;
}

.swiper-pagination-bullet {
    transition: all 0.3s ease;
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background: #fff;
    transform: scale(1.2);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.swiper-slide {
    overflow: hidden;
}

.swiper-slide img {
    will-change: transform;
}

/* Text readability improvements */
.text-shadow-lg {
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 0, 0, 0.2);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
}

/* Water theme gradients */
.from-blue-900\/80 {
    --tw-gradient-from: rgba(30, 58, 138, 0.8);
}

.to-blue-700\/80 {
    --tw-gradient-to: rgba(29, 78, 216, 0.8);
}

.from-cyan-800\/80 {
    --tw-gradient-from: rgba(21, 94, 117, 0.8);
}

.to-teal-700\/80 {
    --tw-gradient-to: rgba(15, 118, 110, 0.8);
}
</style>

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
                <a href="{{ route('sewerage') }}" class="quick-link-card group">
                    <div class="quick-link-icon icon-sewer">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <div class="quick-link-text">New Sewer Connection</div>
                </a>

                <!-- Bill Payment -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-payment">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="quick-link-text">Pay For Services</div>
                </a>

                <!-- Complaints -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-complaint">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="quick-link-text">Lodge A Complaint</div>
                </a>

                <!-- Corruption Report -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-corruption">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="quick-link-text">Report Corruption</div>
                </a>

                <!-- Tenders -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-tenders">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="quick-link-text">Tenders</div>
                </a>

                <!-- Careers -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-careers">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="quick-link-text">Careers</div>
                </a>

                <!-- Reports -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-reports">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="quick-link-text">Reports</div>
                </a>

                <!-- Downloads -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon icon-downloads">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="quick-link-text">Publications & Downloads</div>
                </a>

                <!-- News -->
                <a href="#" class="quick-link-card group">
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
                <a href="#" class="quick-link-card group">
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
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
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
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
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
                    <a href="#" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
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
                    <a href="{{route('support')}}" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
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
                        With personel of experience in water service provision, we continuously strive
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
                    <img src="/img/about-water.jpeg" alt="About NYAWASCO" class="rounded-lg shadow-lg w-full">
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
                    <img src="/img/project-1.jpeg" alt="Water Network Expansion" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Water Network Expansion</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Extending water supply to underserved areas in Nyamira County
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Ongoing</span>
                            <span>Completion: Dec 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Project 2 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/project-2.jpeg" alt="Sewerage System Upgrade" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Sewerage System Upgrade</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Modernizing wastewater treatment facilities for better efficiency
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Ongoing</span>
                            <span>Completion: Apr 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Project 3 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/project-3.jpeg" alt="Digital Metering" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Smart Metering Project</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Installing digital meters for accurate billing and consumption monitoring
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Planning Phase</span>
                            <span>Start: Apr 2026</span>
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
                    <img src="/img/news-1.jpeg" alt="Water Conservation" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">November 15, 2025</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Water Conservation Campaign Launch</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            NYAWASCO launches a new water conservation awareness campaign to promote sustainable water usage...
                        </p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>

                <!-- News 2 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/news-2.jpeg" alt="Infrastructure" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 28, 2025</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">New Water Treatment Plant</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Construction begins on new state-of-the-art water treatment plant to increase capacity...
                        </p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>

                <!-- News 3 -->
                <div class="news-card rounded-lg overflow-hidden">
                    <img src="/img/news-3.jpeg" alt="Community" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 12, 2025</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Community Outreach Program</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            NYAWASCO engages with local communities to address water access challenges...
                        </p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            Read More →
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block">
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
                                    +254 787080455<br>

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
                    <form action="#" method="POST" class="space-y-4">
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
    <div class="emergency-banner mt-2" style="background-color:green;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="font-semibold">Emergency Contact:</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-phone-alt mr-2"></i>
                    <span>+254 787 080 455 (24/7 Emergency Line)</span>
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
