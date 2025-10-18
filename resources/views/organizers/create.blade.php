@extends('layouts.app')

@section('title', $title ?? 'Create Event')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Event</h1>
            <p class="mt-2 text-lg text-gray-600">
                Create a <strong>voting</strong> or <strong>ticketing</strong> event quickly using the form below.
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Basic Info -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Event Name *</label>
                        <input id="name" name="name" type="text" required
                            value="{{ old('name') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g. Miss Culture Kenya">
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                        <select id="category_id" name="category_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="eventType" class="block text-sm font-medium text-gray-700">Event Type *</label>
                        <select id="eventType" name="event_type" required
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Type</option>
                            <option value="ticketed" {{ old('event_type') == 'ticketed' ? 'selected' : '' }}>Ticketed Event</option>
                            <option value="voting" {{ old('event_type') == 'voting' ? 'selected' : '' }}>Voting Event</option>
                            <option value="free" {{ old('event_type') == 'free' ? 'selected' : '' }}>Free Event</option>
                        </select>
                        @error('event_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <textarea id="description" name="description" rows="4" required
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Describe your event...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="featured_image" class="block text-sm font-medium text-gray-700">Event Banner *</label>
                        <input id="featured_image" name="featured_image" type="file" accept="image/*" required
                            class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                        <p class="mt-1 text-sm text-gray-500">Recommended: 1200x600px</p>
                        @error('featured_image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Location -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Location</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div>
                        <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                        <input id="venue_name" name="venue_name" type="text" required
                            value="{{ old('venue_name') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g. KICC Grounds, Nairobi">
                        @error('venue_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="autocomplete" class="block text-sm font-medium text-gray-700">Search or Set Location *</label>
                        <input id="autocomplete" name="address" type="text" required
                            value="{{ old('address') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Search for a location or drag the marker on the map">
                        <div id="map" class="mt-4 h-64 w-full rounded-md border border-gray-300"></div>
                        @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                    </div>
                </div>

                <!-- Event Dates -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Event Details</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date & Time *</label>
                            <input id="start_date" name="start_date" type="datetime-local" required
                                value="{{ old('start_date') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('start_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date & Time *</label>
                            <input id="end_date" name="end_date" type="datetime-local" required
                                value="{{ old('end_date') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('end_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Dynamic Section -->
                <div id="dynamicSection" class="px-6 py-4 border-t border-gray-200 hidden">
                    <div id="ticketSection" class="space-y-3 hidden">
                        <h3 class="text-lg font-medium text-gray-900">Ticket Types</h3>
                        <div id="ticketsList" class="space-y-2"></div>
                        <button type="button" id="addTicketBtn" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                            + Add Ticket Type
                        </button>
                    </div>

                    <div id="votingSection" class="space-y-3 hidden">
                        <h3 class="text-lg font-medium text-gray-900">Contestants</h3>
                        <div id="contestantsList" class="space-y-2"></div>
                        <button type="button" id="addContestantBtn" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                            + Add Contestant
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <a href="{{ route('organizer.events') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm text-sm font-medium">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- OpenStreetMap (Leaflet.js) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('eventType');
    const dynamicSection = document.getElementById('dynamicSection');
    const ticketSection = document.getElementById('ticketSection');
    const votingSection = document.getElementById('votingSection');

    function showSection(type) {
        dynamicSection.classList.add('hidden');
        ticketSection.classList.add('hidden');
        votingSection.classList.add('hidden');

        if (type === 'ticketed') {
            dynamicSection.classList.remove('hidden');
            ticketSection.classList.remove('hidden');
        } else if (type === 'voting') {
            dynamicSection.classList.remove('hidden');
            votingSection.classList.remove('hidden');
        }
    }

    showSection(typeSelect.value);
    typeSelect.addEventListener('change', () => showSection(typeSelect.value));

    // === Free OpenStreetMap Integration ===
    const map = L.map('map').setView([0.0236, 37.9062], 6); // Default: Kenya
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([0.0236, 37.9062], { draggable: true }).addTo(map);

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('autocomplete');

    // Drag marker updates lat/lon
    marker.on('dragend', function () {
        const position = marker.getLatLng();
        latInput.value = position.lat;
        lngInput.value = position.lng;
    });

    // Search address using Nominatim API
    addressInput.addEventListener('change', async () => {
        const query = addressInput.value.trim();
        if (!query) return;
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
        const res = await fetch(url);
        const data = await res.json();
        if (data && data.length > 0) {
            const loc = data[0];
            const lat = parseFloat(loc.lat);
            const lon = parseFloat(loc.lon);
            map.setView([lat, lon], 14);
            marker.setLatLng([lat, lon]);
            latInput.value = lat;
            lngInput.value = lon;
        } else {
            alert("Location not found. Try another search.");
        }
    });
});
</script>
@endsection
