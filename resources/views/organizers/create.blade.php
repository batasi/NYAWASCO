@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('organizer.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    <span class="sr-only">Back to Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('organizer.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Dashboard</a>
                            </li>

                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Create New Event
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Fill in the details below to create your event
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <!-- Event Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Event Name *</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('name') }}"
                            placeholder="Enter event name">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <textarea name="description" id="description" rows="4" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Describe your event...">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                        <textarea name="short_description" id="short_description" rows="2"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Brief description for event cards...">{{ old('short_description') }}</textarea>
                        @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image -->
                    <div>
                        <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image *</label>
                        <input type="file" name="featured_image" id="featured_image" accept="image/*" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x600 pixels</p>
                        @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location Information -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Location Information</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <!-- Venue Name -->
                    <div>
                        <label for="venue_name" class="block text-sm font-medium text-gray-700">Venue Name *</label>
                        <input type="text" name="venue_name" id="venue_name" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('venue_name') }}"
                            placeholder="Enter venue name">
                        @error('venue_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                        <textarea name="address" id="address" rows="3" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Full street address">{{ old('address') }}</textarea>
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City, State, Country -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                            <input type="text" name="city" id="city" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('city') }}"
                                placeholder="City">
                            @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State/Province *</label>
                            <input type="text" name="state" id="state" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('state') }}"
                                placeholder="State">
                            @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">Country *</label>
                            <input type="text" name="country" id="country" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('country') }}"
                                placeholder="Country">
                            @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code *</label>
                        <input type="text" name="postal_code" id="postal_code" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('postal_code') }}"
                            placeholder="Postal code">
                        @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date & Time Information -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Date & Time</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date & Time *</label>
                            <input type="datetime-local" name="start_date" id="start_date" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('start_date') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}">
                            @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date & Time *</label>
                            <input type="datetime-local" name="end_date" id="end_date" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('end_date') }}"
                                min="{{ now()->format('Y-m-d\TH:i') }}">
                            @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Registration Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="registration_start" class="block text-sm font-medium text-gray-700">Registration Start</label>
                            <input type="datetime-local" name="registration_start" id="registration_start"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('registration_start') }}">
                            @error('registration_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="registration_end" class="block text-sm font-medium text-gray-700">Registration End</label>
                            <input type="datetime-local" name="registration_end" id="registration_end"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('registration_end') }}">
                            @error('registration_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing & Capacity -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Pricing & Capacity</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Max Attendees -->
                        <div>
                            <label for="max_attendees" class="block text-sm font-medium text-gray-700">Maximum Attendees</label>
                            <input type="number" name="max_attendees" id="max_attendees" min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('max_attendees') }}"
                                placeholder="Leave empty for unlimited">
                            @error('max_attendees')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                            <input type="number" name="price" id="price" min="0" step="0.01"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('price', 0) }}"
                                placeholder="0.00">
                            @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Free Event -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_free" id="is_free" value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ old('is_free') ? 'checked' : '' }}>
                        <label for="is_free" class="ml-2 block text-sm text-gray-900">
                            This is a free event
                        </label>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Additional Settings</h2>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <!-- Tags -->
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                        <input type="text" name="tags" id="tags"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('tags') }}"
                            placeholder="music, conference, workshop (comma separated)">
                        <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                        @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('organizer.events') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Event
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">Event Creation Tips</h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Provide clear and detailed event descriptions to attract more attendees</span>
                </li>
                <li class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Use high-quality images that represent your event well</span>
                </li>
                <li class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Set realistic capacity limits based on your venue size</span>
                </li>
                <li class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Consider time zones when setting event dates and times</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle free event pricing
        const isFreeCheckbox = document.getElementById('is_free');
        const priceInput = document.getElementById('price');

        function togglePriceField() {
            if (isFreeCheckbox.checked) {
                priceInput.disabled = true;
                priceInput.value = '0';
            } else {
                priceInput.disabled = false;
            }
        }

        isFreeCheckbox.addEventListener('change', togglePriceField);
        togglePriceField(); // Initialize on page load

        // Date validation
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && startDate >= endDate) {
                endDateInput.setCustomValidity('End date must be after start date');
            } else {
                endDateInput.setCustomValidity('');
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    });
</script>
@endsection