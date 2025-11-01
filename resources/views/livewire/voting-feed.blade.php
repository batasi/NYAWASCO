
<div>
@if($contests->count() > 0)
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-purple-500">Explore Upcoming Voting Contests</h2>

    </div>
    <!-- Contests Carousel -->
  <div wire:ignore class="swiper mySwiper-contests px-4 py-6">
    <div class="swiper-wrapper">
        @forelse ($contests as $contest)
            <div class="swiper-slide w-80">
                <a href="{{ route('voting.show', $contest) }}" class="block cursor-pointer">
                    <div class="modal-header shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col h-[360px] group">
                        {{-- Fixed Image Section --}}
                        @if($contest->featured_image)
                            <div class="h-40 w-full bg-black flex items-center justify-center group-hover:opacity-90 transition-opacity">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($contest->featured_image) }}"
                                     alt="{{ $contest->title }}"
                                     class="max-h-full max-w-full object-contain">
                            </div>
                        @else
                            <div class="h-40 w-full bg-gray-200 flex items-center justify-center text-gray-400 group-hover:bg-gray-300 transition-colors">
                                No Image
                            </div>

                        @endif

                        {{-- Content Section --}}
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-purple-400 mb-1 line-clamp-1 group-hover:text-purple-300 transition-colors">
                                    {{ $contest->title }}
                                </h3>
                                <p class="text-white text-sm mb-2">
                                    Category: {{ $contest->category->name ?? 'Uncategorized' }}
                                </p>

                                @if($contest->start_date)
                                    <p class="text-xs text-green-500 mb-1">
                                        Starts: {{ $contest->start_date->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                                @if($contest->end_date)
                                    <p class="text-xs text-yellow-500 mb-2">
                                        Ends: {{ $contest->end_date->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Countdown --}}
                            <p class="text-sm text-blue-400 font-medium countdown mt-2"
                               data-start="{{ $contest->start_date }}"
                               data-end="{{ $contest->end_date }}">
                                <!-- JS will inject countdown here -->
                            </p>

                            {{-- Action Button --}}
                            <div class="mt-4">
                                @if($contest->isOngoing())
                                    <div class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-300">
                                        Vote Now
                                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </div>
                                @elseif($contest->hasEnded())
                                    <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-300">
                                        View Results
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-300">
                                        View Details
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="swiper-slide text-center text-purple-500">No active contests available.</div>
        @endforelse
    </div>

    <div class="swiper-pagination mt-4"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>
    @endif
</div>

<!-- Swiper + Countdown JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.mySwiper-contests', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
         autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.mySwiper-contests .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.mySwiper-contests .swiper-button-next',
            prevEl: '.mySwiper-contests .swiper-button-prev',
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        },
    });

    // Countdown logic
    function updateCountdowns() {
        document.querySelectorAll('.countdown').forEach(el => {
            const start = new Date(el.dataset.start);
            const end = new Date(el.dataset.end);
            const now = new Date();

            let label = '';
            let distance = 0;

            if (now < start) {
                label = 'Starts in ';
                distance = start - now;
            } else if (now >= start && now <= end) {
                label = 'Ends in ';
                distance = end - now;
            } else {
                el.textContent = 'Ended';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            el.textContent = `${label}${days}d ${hours}h ${minutes}m`;
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 60000);
});
</script>
