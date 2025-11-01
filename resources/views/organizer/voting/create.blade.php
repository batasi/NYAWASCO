@extends('layouts.app')

@section('title', $title ?? 'Create Voting Contest')

@section('content')
<div class="min-h-screen modal-header flex items-center justify-center py-10">
    <div class="modal-bg shadow-lg rounded-lg w-full max-w-3xl p-6">
        <h1 class="text-2xl font-bold mb-2" style="color: rgba(198, 0, 238, 1);">Create Voting Contest</h1>
        <p class="text-gray-200 mb-5 text-sm">Create a contest under a category and set voting rules (start/end dates, limits).</p>



        <form method="POST" action="{{ route('voting.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Contest Info --}}
            <div class="space-y-3">
                <div>
                    <label for="title" class="text-sm font-medium text-purple-500">Contest Title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm" style="color: black;"
                        placeholder="Example: Htz Awards 2019">
                </div>

                {{-- Voting Category --}}
                <div>
                    <label for="category_id" class="text-sm font-medium text-purple-500">Voting Category</label>
                    <div class="flex gap-2">
                        <select id="category_id" name="category_id" required
                            class="flex-1 border-gray-300 rounded-md px-3 py-1.5 text-sm" style="color: black;">
                            <option value="">-- Select Voting Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="addVotingCategoryBtn"
                            class="px-3 py-1.5 bg-purple-500 text-white rounded-md text-sm hover:bg-indigo-700">+</button>
                    </div>
                </div>

                <div>
                    <label for="description" class="text-sm font-medium text-purple-500">Description (optional)</label>
                    <textarea id="description" name="description" rows="2"
                        class="w-full border-gray-300 rounded-md px-3 py-1.5 text-sm" style="color: black;">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="description" class="block text-sm font-medium text-purple-400 mb-2">Description (optional)</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">{{ old('description') }}</textarea>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-purple-400 mb-2">Start Date</label>
                            <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-purple-400 mb-2">End Date</label>
                            <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                        </div>
                    </div>

                    <!-- Contest Banner -->
                    <div>
                        <label for="featured_image" class="block text-sm font-medium text-purple-400 mb-2">Contest Banner (optional)</label>
                        <input id="featured_image" name="featured_image" type="file" accept="image/*"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-600 file:text-white hover:file:bg-purple-700 transition-colors duration-200">

                        <!-- Preview -->
                        <div id="featuredImagePreviewContainer" class="mt-4 hidden">
                            <img id="featuredImagePreview" src="#" alt="Banner Preview"
                                class="rounded-lg w-full max-w-md h-48 object-cover border-2 border-gray-600">
                        </div>
                    </div>

                    <!-- Contest Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-purple-400 mb-2">Contest Fee (optional)</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0"
                            value="{{ old('amount') }}"
                            class="w-full bg-gray-200 border border-gray-600 rounded-lg px-4 py-3 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                            placeholder="Enter amount (e.g. 5.00)">
                        <p class="text-xs text-gray-400 mt-2">Leave empty if there's no fee for this contest.</p>
                    </div>

                    <!-- Voting Limits -->
                    <div class="bg-gray-750 rounded-lg p-6 border border-gray-600">
                        <label class="block text-sm font-medium text-purple-400 mb-4">Voting Limits</label>

                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center text-sm text-white">
                                <input type="radio" name="votes_limit_type" value="limited" id="votes_limited"
                                    {{ old('votes_limit_type', 'limited') == 'limited' ? 'checked' : '' }}
                                    class="h-4 w-4 text-purple-600 border-gray-500 bg-gray-700 focus:ring-purple-500 focus:ring-offset-gray-800">
                                <span class="ml-3">Limited Votes</span>
                            </label>
                            <label class="flex items-center text-sm text-white">
                                <input type="radio" name="votes_limit_type" value="unlimited" id="votes_unlimited"
                                    {{ old('votes_limit_type') == 'unlimited' ? 'checked' : '' }}
                                    class="h-4 w-4 text-purple-600 border-gray-500 bg-gray-700 focus:ring-purple-500 focus:ring-offset-gray-800">
                                <span class="ml-3">Unlimited Votes</span>
                            </label>
                        </div>

                        <div id="maxVotesWrapper">
                            <label for="max_votes_per_user" class="block text-sm font-medium text-purple-400 mb-2">Max Votes Per User</label>
                            <input id="max_votes_per_user" name="max_votes_per_user" type="number" min="1"
                                value="{{ old('max_votes_per_user', 1) }}"
                                class="w-full bg-gray-200 border border-gray-600 rounded-lg px-4 py-3 text-black focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                            <p class="text-xs text-gray-400 mt-2">If 'Unlimited' is selected above, this value will be ignored.</p>
                        </div>
                    </div>

                <div class="grid grid-cols-2 gap-3 mt-3" style="display:none">
                    <div class="flex items-center gap-2 mt-6">
                        <input id="is_featured" name="is_featured" type="checkbox" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="is_featured" class="text-purple-500 text-sm">Feature this contest</label>
                    </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-purple-600 border-gray-500 bg-gray-700 rounded focus:ring-purple-500 focus:ring-offset-gray-800">
                            <label for="is_active" class="text-sm font-medium text-white">Activate contest</label>
                        </div>
                    </div>

                <div class="flex flex-wrap items-center gap-4 mt-2" style="display:none">
                    <label class="flex items-center text-sm">
                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1"
                            {{ old('requires_approval') ? 'checked' : '' }}
                            class="h-4 w-4 text-purple-600 border-gray-500 bg-gray-700 rounded focus:ring-purple-500 focus:ring-offset-gray-800">
                        <label for="requires_approval" class="text-sm font-medium text-white">Require admin approval</label>
                    </div>
                </div>

                <!-- Nominees Section -->
                <div class="border-t border-gray-600 pt-8">
                    <h2 class="text-xl font-bold text-white mb-6">Add Nominees</h2>

                    <div id="nomineesWrapper" class="space-y-4">
                        <!-- Default Nominee Item -->
                        <div class="nominee-item bg-gray-750 rounded-lg border border-gray-600 p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-purple-400 mb-2">Name</label>
                                    <input type="text" name="nominees[0][name]" required
                                        class="w-full bg-gray-200 border border-gray-600 rounded-lg px-3 py-2 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        placeholder="Nominee Name">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-purple-400 mb-2">Photo</label>
                                    <input type="file" name="nominees[0][photo]" accept="image/*"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-purple-600 file:text-white hover:file:bg-purple-700 transition-colors duration-200">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-purple-400 mb-2">Description</label>
                                    <input type="text" name="nominees[0][description]"
                                        class="w-full bg-gray-200 border border-gray-600 rounded-lg px-3 py-2 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        placeholder="Short description">
                                </div>

                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-purple-400 mb-2">Category</label>
                                        <select name="nominees[0][category_id]" required
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                                            <option value="" class="text-gray-400">-- Select --</option>
                                            @foreach($nomineeCategories as $ncat)
                                                <option value="{{ $ncat->id }}" class="text-white">{{ $ncat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="openNomineeCatModal px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200 flex items-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="removeNomineeBtn mt-4 text-red-400 hover:text-red-300 font-bold text-lg transition-colors duration-200 flex items-center gap-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" id="addNomineeBtn"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Nominee
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-8 border-t border-gray-600">
                    <a href="{{ route('voting.index') }}"
                        class="px-6 py-3 border border-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Create Contest
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Voting Category Modal -->
<div id="votingCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-600 w-full max-w-md p-6">
        <h2 class="text-xl font-bold text-white mb-4">Add Voting Category</h2>
        <form id="votingCategoryForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-purple-400 mb-2">Category Name</label>
                <input
                    type="text"
                    id="votingCategoryName"
                    name="name"
                    placeholder="Enter category name"
                    required
                    class="w-full bg-gray-200 border border-gray-600 rounded-lg px-4 py-3 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-purple-400 mb-2">Color (optional)</label>
                <input
                    type="color"
                    id="votingCategoryColor"
                    name="color"
                    value="#8B5CF6"
                    class="w-20 h-10 border border-gray-600 rounded-lg cursor-pointer"
                >
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button
                    type="button"
                    id="cancelVotingCategoryBtn"
                    class="px-4 py-2 border border-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors duration-200"
                >
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Nominee Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-600 w-full max-w-md p-6">
        <h2 class="text-xl font-bold text-white mb-4">Add Nominee Category</h2>
        <form id="categoryForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-purple-400 mb-2">Category Name</label>
                <input type="text" id="categoryName" name="name" placeholder="Enter category name" required
                    class="w-full bg-gray-200 border border-gray-600 rounded-lg px-4 py-3 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-purple-400 mb-2">Color (optional)</label>
                <input type="color" id="categoryColor" name="color" value="#8B5CF6"
                    class="w-20 h-10 border border-gray-600 rounded-lg cursor-pointer">
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelCategoryBtn"
                    class="px-4 py-2 border border-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors duration-200">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }

    .modal-bg {
        background: transparent;
    }

    .bg-gray-750 {
        background-color: #374151;
    }

    /* Input fields with light backgrounds - increased specificity */
    input.bg-gray-200,
    textarea.bg-gray-200,
    #votingCategoryModal input[type="text"],
    #categoryModal input[type="text"] {
        color: #000000 !important; /* Black text for light backgrounds */
        background-color: #e5e7eb !important; /* Light gray background */
        border: 1px solid #9ca3af !important;
    }

    /* Placeholder text for light background inputs */
    input.bg-gray-200::placeholder,
    textarea.bg-gray-200::placeholder,
    #votingCategoryModal input[type="text"]::placeholder,
    #categoryModal input[type="text"]::placeholder {
        color: #6b7280 !important; /* Darker gray for readability */
    }

    /* Focus state for light background inputs */
    input.bg-gray-200:focus,
    textarea.bg-gray-200:focus,
    #votingCategoryModal input[type="text"]:focus,
    #categoryModal input[type="text"]:focus {
        background-color: #f3f4f6 !important; /* Slightly lighter on focus */
        color: #000000 !important; /* Ensure text remains black */
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25) !important;
        outline: none !important;
    }

    /* Input fields with dark backgrounds */
    input.bg-gray-700,
    textarea.bg-gray-700 {
        color: #ffffff !important; /* White text for dark backgrounds */
        background-color: #374151 !important;
        border: 1px solid #4b5563 !important;
    }

    /* Placeholder text for dark background inputs */
    input.bg-gray-700::placeholder,
    textarea.bg-gray-700::placeholder {
        color: #9ca3af !important;
    }

    /* SELECT ELEMENTS - FIX FOR DROPDOWNS */
    select.bg-gray-700 {
        color: #ffffff !important;
        background-color: #374151 !important;
        border: 1px solid #4b5563 !important;
    }

    /* Dropdown options styling */
    select.bg-gray-700 option {
        background-color: #374151 !important;
        color: #ffffff !important;
        padding: 8px 12px;
    }

    /* Selected option */
    select.bg-gray-700 option:checked {
        background-color: #7c3aed !important;
        color: #ffffff !important;
    }

    /* Hover state for options (limited browser support) */
    select.bg-gray-700 option:hover {
        background-color: #6d28d9 !important;
    }

    /* Focus state for select */
    select.bg-gray-700:focus {
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25) !important;
        outline: none !important;
    }

    /* Custom file input styling */
    input[type="file"]::-webkit-file-upload-button {
        background-color: #7c3aed;
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        margin-right: 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    input[type="file"]::-webkit-file-upload-button:hover {
        background-color: #6d28d9;
    }

    /* Custom color input styling */
    input[type="color"] {
        -webkit-appearance: none;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    input[type="color"]::-webkit-color-swatch {
        border: 2px solid #4b5563;
        border-radius: 6px;
    }

    /* Custom scrollbar for selects */
    select {
        scrollbar-width: thin;
        scrollbar-color: #7c3aed #374151;
    }

    select::-webkit-scrollbar {
        width: 8px;
    }

    select::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb {
        background: #7c3aed;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb:hover {
        background: #6d28d9;
    }

    /* Ensure modal content is visible */
    #votingCategoryModal .bg-gray-800,
    #categoryModal .bg-gray-800 {
        background-color: #1f2937 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle votes limit toggle
        const votesLimitedRadio = document.getElementById('votes_limited');
        const votesUnlimitedRadio = document.getElementById('votes_unlimited');
        const maxVotesWrapper = document.getElementById('maxVotesWrapper');
        const maxVotesInput = document.getElementById('max_votes_per_user');

        function updateMaxVotesVisibility() {
            if (votesUnlimitedRadio && votesUnlimitedRadio.checked) {
                maxVotesWrapper.style.display = 'none';
                if (maxVotesInput) maxVotesInput.disabled = true;
            } else {
                maxVotesWrapper.style.display = 'block';
                if (maxVotesInput) maxVotesInput.disabled = false;
            }
        }

        if (votesLimitedRadio && votesUnlimitedRadio) {
            votesLimitedRadio.addEventListener('change', updateMaxVotesVisibility);
            votesUnlimitedRadio.addEventListener('change', updateMaxVotesVisibility);
            updateMaxVotesVisibility();
        }

        // Featured image preview
        const featuredImageInput = document.getElementById('featured_image');
        const featuredImagePreview = document.getElementById('featuredImagePreview');
        const featuredImagePreviewContainer = document.getElementById('featuredImagePreviewContainer');

        if (featuredImageInput) {
            featuredImageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file && featuredImagePreview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        featuredImagePreview.src = e.target.result;
                        featuredImagePreviewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else if (featuredImagePreviewContainer) {
                    featuredImagePreviewContainer.classList.add('hidden');
                }
            });
        }

        // Nominees management
        let nomineeIndex = 1;
        const addNomineeBtn = document.getElementById('addNomineeBtn');
        const nomineesWrapper = document.getElementById('nomineesWrapper');

        addNomineeBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.classList.add('nominee-item', 'bg-gray-750', 'rounded-lg', 'border', 'border-gray-600', 'p-6');
            div.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-purple-400 mb-2">Name</label>
                        <input type="text" name="nominees[${nomineeIndex}][name]" required
                            class="w-full bg-gray-200 border border-gray-600 rounded-lg px-3 py-2 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                            placeholder="Nominee Name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-400 mb-2">Photo</label>
                        <input type="file" name="nominees[${nomineeIndex}][photo]" accept="image/*"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-purple-600 file:text-white hover:file:bg-purple-700 transition-colors duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-400 mb-2">Description</label>
                        <input type="text" name="nominees[${nomineeIndex}][description]"
                            class="w-full bg-gray-200 border border-gray-600 rounded-lg px-3 py-2 text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                            placeholder="Short description">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-purple-400 mb-2">Category</label>
                            <select name="nominees[${nomineeIndex}][category_id]" required
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                                <option value="" class="text-gray-400">-- Select --</option>
                                @foreach($nomineeCategories as $ncat)
                                <option value="{{ $ncat->id }}" class="text-white">{{ $ncat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="openNomineeCatModal px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" class="removeNomineeBtn mt-4 text-red-400 hover:text-red-300 font-bold text-lg transition-colors duration-200 flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remove
                </button>
            `;
            nomineesWrapper.appendChild(div);
            nomineeIndex++;
        });

        // Remove nominee
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeNomineeBtn')) {
                e.target.closest('.nominee-item').remove();
            }
        });

        // Voting Category Modal
        const votingModal = document.getElementById('votingCategoryModal');
        const cancelVotingBtn = document.getElementById('cancelVotingCategoryBtn');
        const votingForm = document.getElementById('votingCategoryForm');
        const addVotingBtn = document.getElementById('addVotingCategoryBtn');

        if (addVotingBtn) {
            addVotingBtn.addEventListener('click', () => {
                votingModal.classList.remove('hidden');
                votingModal.classList.add('flex');
            });
        }

        if (cancelVotingBtn) {
            cancelVotingBtn.addEventListener('click', () => {
                votingModal.classList.add('hidden');
                votingModal.classList.remove('flex');
                if (votingForm) votingForm.reset();
            });
        }

        // AJAX voting category submit
        if (votingForm) {
            votingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = document.getElementById('votingCategoryName').value.trim();
                const color = document.getElementById('votingCategoryColor').value;
                if(!name) return alert('Category name is required');

                fetch("{{ route('voting-category.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name, color })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const select = document.getElementById('category_id');
                        const opt = document.createElement('option');
                        opt.value = data.category.id;
                        opt.text = data.category.name;
                        opt.selected = true;
                        select.appendChild(opt);

                        votingModal.classList.add('hidden');
                        votingModal.classList.remove('flex');
                        votingForm.reset();
                    } else {
                        alert(data.message || 'Error adding category');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong');
                });
            });
        }

        // Nominee Category Modal
        const nomineeModal = document.getElementById('categoryModal');
        const cancelNomineeBtn = document.getElementById('cancelCategoryBtn');
        const nomineeForm = document.getElementById('categoryForm');

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('openNomineeCatModal')) {
                nomineeModal.classList.remove('hidden');
                nomineeModal.classList.add('flex');
            }
        });

        if (cancelNomineeBtn) {
            cancelNomineeBtn.addEventListener('click', () => {
                nomineeModal.classList.add('hidden');
                nomineeModal.classList.remove('flex');
                if (nomineeForm) nomineeForm.reset();
            });
        }

        // AJAX nominee category submit
        if (nomineeForm) {
            nomineeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = document.getElementById('categoryName').value.trim();
                const color = document.getElementById('categoryColor').value;
                if(!name) return alert('Category name is required');

                fetch("{{ route('nominee-categories.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name, color })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const selects = document.querySelectorAll('select[name^="nominees"]');
                        selects.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = data.category.id;
                            opt.text = data.category.name;
                            opt.selected = true;
                            s.appendChild(opt);
                        });
                        nomineeModal.classList.add('hidden');
                        nomineeModal.classList.remove('flex');
                        nomineeForm.reset();
                    } else {
                        alert(data.message || 'Error adding category');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong');
                });
            });
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const votingModal = document.getElementById('votingCategoryModal');
            const nomineeModal = document.getElementById('categoryModal');

            if (event.target === votingModal) {
                votingModal.classList.add('hidden');
                votingModal.classList.remove('flex');
            }
            if (event.target === nomineeModal) {
                nomineeModal.classList.add('hidden');
                nomineeModal.classList.remove('flex');
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const votingModal = document.getElementById('votingCategoryModal');
                const nomineeModal = document.getElementById('categoryModal');

                if (votingModal && !votingModal.classList.contains('hidden')) {
                    votingModal.classList.add('hidden');
                    votingModal.classList.remove('flex');
                }
                if (nomineeModal && !nomineeModal.classList.contains('hidden')) {
                    nomineeModal.classList.add('hidden');
                    nomineeModal.classList.remove('flex');
                }
            }
        });
    });
</script>
@endsection
