
<div>
@if($contests->count() > 0)
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Explore Upcoming Voting Contests</h2>

    </div>
    <!-- Contests Carousel -->
    <div wire:ignore class="swiper mySwiper-contests px-4 py-6">
        <div class="swiper-wrapper">
            @forelse ($contests as $contest)
                <div class="swiper-slide w-80">
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col h-[360px]">
                        {{-- Fixed Image Section --}}
                        @if($contest->featured_image)
                            <div class="h-40 w-full bg-gray-100 flex items-center justify-center">
                                   <img
                                    src="{{ $contest->featured_image
                                        ?  \Illuminate\Support\Facades\Storage::url($contest->featured_image)
                                        : asset('voting-contests/banner1.jpg') }}"
                                    alt="{{ $contest->title }}"
                                    class="w-full h-64 object-contain rounded-lg"
                                />
                            </div>
                        @else
                            <div class="h-40 w-full bg-gray-100 flex items-center justify-center">

                                <img
                                    src="{{ $contest->featured_image
                                        ?  \Illuminate\Support\Facades\Storage::url($contest->featured_image)
                                        : asset('voting-contests/banner1.jpg') }}"
                                    alt="{{ $contest->title }}"
                                    class="w-full h-64 object-contain rounded-lg"
                                />
                            </div>

                        @endif

                        {{-- Content Section --}}
                        <div class="flex-1 p-4 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-1 line-clamp-1">
                                    {{ $contest->title }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-2">
                                    Category: {{ $contest->category->name ?? 'Uncategorized' }}
                                </p>

                                @if($contest->start_date)
                                    <p class="text-xs text-gray-500 mb-1">
                                        Starts: {{ $contest->start_date->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                                @if($contest->end_date)
                                    <p class="text-xs text-gray-500 mb-2">
                                        Ends: {{ $contest->end_date->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Countdown --}}
                            <p class="text-sm text-indigo-600 font-medium countdown mt-2"
                               data-start="{{ $contest->start_date }}"
                               data-end="{{ $contest->end_date }}">
                                <!-- JS will inject countdown here -->
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="swiper-slide text-center text-gray-500">No active contests available.</div>
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
