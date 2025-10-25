
<div>
@if($events->count() > 0)
<div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Explore new Events</h2>

</div>
    <div wire:ignore class="swiper mySwiper-events px-4 py-6">
        <div class="swiper-wrapper">
            @forelse($events as $event)
                <div class="swiper-slide">
                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-xl transition">
                           <img
                                    src="{{ $event->featured_image
                                        ?  \Illuminate\Support\Facades\Storage::url($event->featured_image)
                                        : asset('voting-contests/banner1.jpg') }}"

                                    class="w-full h-64 object-contain rounded-lg"
                                />
                        <h3 class="font-semibold text-lg">{{ $event->name }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ \Illuminate\Support\Str::limit($event->short_description ?? $event->description, 100) }}
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="text-indigo-600 mt-2 inline-block hover:underline">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="swiper-slide text-center text-gray-500">No upcoming events at the moment.</div>
            @endforelse
        </div>

        <div class="swiper-pagination mt-4"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
    @endif
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Swipers
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

    new Swiper('.mySwiper-events', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        pagination: {
            el: '.mySwiper-events .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.mySwiper-events .swiper-button-next',
            prevEl: '.mySwiper-events .swiper-button-prev',
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
    setInterval(updateCountdowns, 60000); // update every minute
});
</script>
