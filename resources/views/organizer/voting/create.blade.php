@extends('layouts.app')

@section('title', $title ?? 'Create Voting Contest')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Voting Contest</h1>
            <p class="mt-2 text-lg text-gray-600">
                Create a contest under a category and set voting rules (start/end dates, limits).
            </p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white shadow rounded-lg p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('voting.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Contest Title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required
                        class="w-full border-gray-300 rounded-md px-3 py-2" placeholder="Best Male Artist 2025">
                </div>

                {{-- Category Selection --}}
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-medium mb-2">Category</label>
                    <div class="flex gap-2">
                        <select id="category_id" name="category_id" required
                            class="flex-1 border-gray-300 rounded-md px-3 py-2">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="addCategoryBtn"
                            class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            +
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description (optional)</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full border-gray-300 rounded-md px-3 py-2">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-gray-700 font-medium mb-2">Start Date</label>
                        <input id="start_date" name="start_date" type="date"
                            value="{{ old('start_date') }}"
                            class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700 font-medium mb-2">End Date</label>
                        <input id="end_date" name="end_date" type="date"
                            value="{{ old('end_date') }}"
                            class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="max_votes_per_user" class="block text-gray-700 font-medium mb-2">Max Votes Per User</label>
                        <input id="max_votes_per_user" name="max_votes_per_user" type="number" min="1"
                            value="{{ old('max_votes_per_user', 1) }}"
                            class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>

                    <div class="flex items-center mt-6">
                        <input id="is_featured" name="is_featured" type="checkbox" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 text-gray-700">Feature this contest</label>
                    </div>
                </div>

                <div class="flex items-center gap-6 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <span class="ml-2 text-gray-700">Activate contest</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1"
                            {{ old('requires_approval') ? 'checked' : '' }}
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <span class="ml-2 text-gray-700">Require admin approval for nominees/votes</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('voting.index') }}"
                        class="mr-3 inline-block px-4 py-2 border rounded-md text-gray-700 bg-gray-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
                        Create Contest
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal for Adding Category --}}
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-xl font-bold mb-4">Add New Category</h2>
        <form id="categoryForm">
            @csrf
            <div class="mb-3">
                <label class="block text-gray-700 font-medium mb-1">Category Name</label>
                <input type="text" id="categoryName" name="name" placeholder="Enter category name"
                    class="w-full border-gray-300 rounded-md px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Color (optional)</label>
                <input type="color" id="categoryColor" name="color" value="#8B5CF6"
                    class="w-16 h-10 border rounded-md">
            </div>

            <div class="flex justify-end">
                <button type="button" id="cancelCategoryBtn"
                    class="mr-2 px-4 py-2 bg-gray-200 rounded-md">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Category Modal Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    const addBtn = document.getElementById('addCategoryBtn');
    const cancelBtn = document.getElementById('cancelCategoryBtn');
    const form = document.getElementById('categoryForm');

    addBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('categoryName').value;
        const color = document.getElementById('categoryColor').value;

        fetch("{{ route('voting-category.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name, color })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('category_id');
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                select.appendChild(option);

                modal.classList.add('hidden');
                form.reset();
                alert('Category added successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to add category'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong');
        });
    });
});
</script>

@endsection
