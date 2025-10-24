<!-- resources/views/livewire/voting-feed.blade.php -->
<div>
    <div wire:ignore class="swiper mySwiper px-4 py-6">
        <div class="swiper-wrapper">
            @forelse ($contests as $contest)
                <div class="swiper-slide">
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition duration-300">
                        @if($contest->image)
                            <img src="{{ Storage::url($contest->image) }}" alt="{{ $contest->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $contest->title }}</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Category: {{ $contest->category->name ?? 'Uncategorized' }}
                            </p>

                            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                                @foreach ($contest->nominees as $nominee)
                                    <li>• {{ $nominee->name }}</li>
                                @endforeach
                            </ul>

                            <p class="text-xs text-gray-500">
                                Ends: {{ $contest->end_date ? $contest->end_date->format('M d, Y') : 'Ongoing' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="swiper-slide text-center text-gray-500">No active contests available.</div>
            @endforelse
        </div>

        <!-- Pagination + Navigation -->
        <div class="swiper-pagination mt-4"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    @if ($contests->count() >= $perPage)
        <div class="text-center mt-6">
            <button wire:click="loadMore" class="px-6 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
                Load More
            </button>
        </div>
    @endif
</div>
