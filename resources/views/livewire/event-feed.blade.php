<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($events as $event)
    <div class="bg-white rounded-lg shadow p-4">
        <img src="{{ $event->featured_image }}" alt="{{ $event->name }}" class="rounded mb-4">
        <h3 class="font-semibold text-lg">{{ $event->name }}</h3>
        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($event->short_description ?? $event->description, 100) }}</p>
        <a href="{{ route('events.show', $event) }}" class="text-blue-500 mt-2 inline-block">View Details</a>
    </div>
    @empty
    <p class="col-span-3 text-center text-gray-500">No upcoming events at the moment.</p>
    @endforelse
</div>