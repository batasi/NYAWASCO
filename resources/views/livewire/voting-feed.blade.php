<div class="voting-feed-container grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse ($contests as $contest)
    <div class="bg-white shadow-md rounded-xl overflow-hidden hover:shadow-lg transition duration-300">
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                {{ $contest->title }}
            </h3>
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
    @empty
    <p class="col-span-full text-center text-gray-500">No active contests available.</p>
    @endforelse

    @if ($contests->count() >= $perPage)
    <div class="col-span-full text-center mt-6">
        <button wire:click="loadMore" class="px-6 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
            Load More
        </button>
    </div>
    @endif
</div>