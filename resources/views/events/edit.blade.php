@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        Edit Event: {{ $event->title }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        Update your event details and settings
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="{{ route('events.show', $event) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Event
                    </a>
                    <a href="{{ route('organizer.events') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-600 text-white p-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-600 text-white p-4 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organizer.events.update', $event) }}" method="POST" enctype="multipart/form-data" id="eventForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Event Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>

                        <div class="space-y-4">
                            <!-- Event Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-200 mb-2">
                                    Event Title <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}"
                                       class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                       placeholder="Enter your event title" required maxlength="191">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-200 mb-2">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="5"
                                          class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                          placeholder="Describe your event in detail">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-200 mb-2">
                                    Category <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <select id="category_id" name="category_id"
                                            class="w-full px-4 py-3 pr-12 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 appearance-none" required>
                                        <option value="" class="text-gray-500">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }} class="text-white">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Add Category Button -->
                                    <button type="button"
                                            onclick="openCategoryModal()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1.5 text-purple-400 hover:text-purple-300 hover:bg-purple-900/30 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Date & Time</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-200 mb-2">
                                    Start Date & Time <span class="text-red-400">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date"
                                       value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200" required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-200 mb-2">
                                    End Date & Time <span class="text-red-400">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date"
                                       value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200" required>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location & Pricing Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Location & Pricing</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-200 mb-2">
                                    Location <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="location" name="location" value="{{ old('location', $event->location) }}"
                                       class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                       placeholder="Event venue or address" required maxlength="191">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="ticket_price" class="block text-sm font-medium text-gray-200 mb-2">
                                        Ticket Price (KES)
                                    </label>
                                    <input type="number" id="ticket_price" name="ticket_price" value="{{ old('ticket_price', $event->ticket_price) }}"
                                           class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('ticket_price')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-200 mb-2">
                                        Capacity
                                    </label>
                                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $event->capacity) }}"
                                           class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="Maximum number of attendees" min="1">
                                    @error('capacity')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Image Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Image</h3>

                        <div class="space-y-4">
                            @if($event->banner_image)
                            <div>
                                <label class="block text-sm font-medium text-gray-200 mb-2">Current Image</label>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ Storage::disk('public')->url($event->banner_image) }}"
                                         alt="Event banner"
                                         class="w-32 h-20 object-cover rounded-lg border border-gray-600">
                                    <div class="text-sm text-gray-400">
                                        <p>Current banner image</p>
                                        <button type="button"
                                                onclick="document.getElementById('remove_banner').value = '1'; document.getElementById('current_banner').classList.add('hidden');"
                                                class="text-red-400 hover:text-red-300 mt-1">
                                            Remove image
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="remove_banner" name="remove_banner" value="0">
                                <div id="current_banner">
                                    <input type="hidden" name="current_banner" value="{{ $event->banner_image }}">
                                </div>
                            </div>
                            @endif

                            <div>
                                <label for="banner_image" class="block text-sm font-medium text-gray-200 mb-2">
                                    {{ $event->banner_image ? 'Update Banner Image' : 'Banner Image' }}
                                </label>
                                <input type="file" id="banner_image" name="banner_image"
                                       class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                       accept="image/*">
                                <p class="mt-2 text-sm text-gray-400">Recommended size: 1200x400 pixels. Max file size: 2MB</p>
                                @error('banner_image')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Event Status Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Status</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-200 mb-2">
                                    Status
                                </label>
                                <select id="status" name="status"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                                    <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }} class="text-white">Draft</option>
                                    <option value="pending_approval" {{ old('status', $event->status) == 'pending_approval' ? 'selected' : '' }} class="text-white">Pending Approval</option>
                                    <option value="approved" {{ old('status', $event->status) == 'approved' ? 'selected' : '' }} class="text-white">Approved</option>
                                    <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }} class="text-white">Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Event Settings Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Settings</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label for="is_featured" class="text-sm font-medium text-gray-200">Feature this event</label>
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                       {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="is_active" class="text-sm font-medium text-gray-200">Activate event</label>
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $event->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            </div>
                        </div>
                    </div>

                    <!-- Event Statistics Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Statistics</h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Created</span>
                                <span class="text-white">{{ $event->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Last Updated</span>
                                <span class="text-white">{{ $event->updated_at->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status</span>
                                <span class="capitalize {{ $event->status == 'approved' ? 'text-green-400' : ($event->status == 'cancelled' ? 'text-red-400' : 'text-yellow-400') }}">
                                    {{ str_replace('_', ' ', $event->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Event
                            </button>

                            <a href="{{ route('organizer.events') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Danger Zone Section -->
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-6 mt-8">
            <h3 class="text-lg font-semibold text-red-800 mb-4">Danger Zone</h3>

            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-red-800">Delete Event</h4>
                    <p class="text-sm text-red-600 mt-1">
                        Once you delete an event, there is no going back. Please be certain.
                    </p>

                    @if($event->bookings()->exists() || $event->ticketPurchases()->exists())
                        <p class="text-sm text-red-700 font-medium mt-2">
                            ⚠️ This event has existing bookings or ticket purchases and cannot be deleted.
                        </p>
                    @endif
                </div>

                <div>
                    @if(!$event->bookings()->exists() && !$event->ticketPurchases()->exists())
                        <button type="button"
                                onclick="openDeleteModal('{{ $event->id }}', '{{ $event->title }}')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4
                                    a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Event
                        </button>

                        <!-- Delete Form (Hidden, submitted via JS) -->
                        <form id="delete-form-{{ $event->id }}"
                            action="{{ route('organizer.events.destroy', $event) }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @else
                        <button disabled
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-400 cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                                    2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732
                                    0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Cannot Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal"
            class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 px-4 sm:px-6 lg:px-8">

            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl p-6 md:p-8 transform transition-all duration-300 scale-100">

                <!-- Header -->
                <h2 class="text-lg md:text-xl lg:text-2xl font-semibold text-gray-900 mb-4 text-center">
                    Confirm Deletion
                </h2>

                <!-- Message -->
                <p class="text-gray-700 mb-6 text-sm md:text-base text-center leading-relaxed" id="deleteModalMessage">
                    Are you sure you want to delete this item? This action cannot be undone.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center sm:justify-end gap-3 mt-6">
                    <button type="button"
                        onclick="closeDeleteModal()"
                        class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">
                        Cancel
                    </button>

                    <button type="button"
                        id="confirmDeleteButton"
                        class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Category Modal (same as create form) -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm md:max-w-md lg:max-w-lg border border-gray-700 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Add New Category</h3>
            <button type="button"
                    onclick="closeCategoryModal()"
                    class="text-gray-400 hover:text-gray-300 transition-colors duration-200 p-1 rounded-lg hover:bg-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="categoryForm" method="POST" action="{{ route('event-categories.store') }}">
            @csrf
            <div class="p-6 space-y-4">
                <!-- Category Name -->
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-200 mb-2">
                        Category Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           id="category_name"
                           name="name"
                           required
                           class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                           placeholder="Enter category name">
                    <div id="category_name_error" class="mt-1 text-sm text-red-400 hidden"></div>
                </div>

                <!-- Description -->
                <div>
                    <label for="category_description" class="block text-sm font-medium text-gray-200 mb-2">
                        Description
                    </label>
                    <textarea id="category_description"
                              name="description"
                              rows="3"
                              class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 resize-none"
                              placeholder="Brief description of the category"></textarea>
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_icon" class="block text-sm font-medium text-gray-200 mb-2">
                            Icon
                        </label>
                        <select id="category_icon" name="icon"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                            <option value="">Select Icon</option>
                            <option value="music">Music</option>
                            <option value="trophy">Sports</option>
                            <option value="briefcase">Business</option>
                            <option value="palette">Art</option>
                            <option value="utensils">Food</option>
                            <option value="code">Technology</option>
                            <option value="heart">Charity</option>
                            <option value="tool">Workshop</option>
                            <option value="drama">Theater</option>
                            <option value="users">Networking</option>
                        </select>
                    </div>

                    <div>
                        <label for="category_color" class="block text-sm font-medium text-gray-200 mb-2">
                            Color
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color"
                                   id="category_color"
                                   name="color"
                                   value="#3B82F6"
                                   class="w-12 h-12 bg-gray-900 border border-gray-600 rounded-lg cursor-pointer">
                            <input type="text"
                                   id="category_color_text"
                                   value="#3B82F6"
                                   class="flex-1 px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                   placeholder="#3B82F6">
                        </div>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox"
                           id="category_active"
                           name="is_active"
                           value="1"
                           checked
                           class="w-4 h-4 text-purple-600 bg-gray-900 border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                    <label for="category_active" class="ml-2 text-sm text-gray-200">
                        Active Category
                    </label>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-700">
                <button type="button"
                        onclick="closeCategoryModal()"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit"
                        id="saveCategoryBtn"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-semibold">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        if (endDate <= startDate) {
            e.preventDefault();
            alert('End date must be after start date.');
            return false;
        }
    });

    // Category Modal Functions
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('category_name').focus();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('categoryForm').reset();
        document.getElementById('category_name_error').classList.add('hidden');
    }

    // Category Form Submission
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const saveBtn = document.getElementById('saveCategoryBtn');
        const originalText = saveBtn.textContent;

        // Show loading state
        saveBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        saveBtn.disabled = true;

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                closeCategoryModal();
                const categorySelect = document.getElementById('category_id');
                const newOption = new Option(data.category.name, data.category.id, false, true);
                categorySelect.add(newOption);
            } else {
                // Handle errors
                console.error('Error:', data);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('categoryModal').classList.contains('hidden')) {
            closeCategoryModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });
</script>

<script>
    let currentEventId = null;

    function openDeleteModal(eventId, eventTitle) {
        currentEventId = eventId;
        const message = `You are about to permanently delete "${eventTitle}". This action cannot be undone.`;
        document.getElementById('deleteModalMessage').textContent = message;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        currentEventId = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        if (currentEventId) {
            const form = document.getElementById(`delete-form-${currentEventId}`);
            if (form) form.submit();
        }
    });
</script>

@endpush
@endsection
