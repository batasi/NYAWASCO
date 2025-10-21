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

                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-medium mb-2">Category</label>
                    <select id="category_id" name="category_id" required class="w-full border-gray-300 rounded-md px-3 py-2">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description (optional)</label>
                    <textarea id="description" name="description" rows="3" class="w-full border-gray-300 rounded-md px-3 py-2">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-gray-700 font-medium mb-2">Start Date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700 font-medium mb-2">End Date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="max_votes_per_user" class="block text-gray-700 font-medium mb-2">Max Votes Per User</label>
                        <input id="max_votes_per_user" name="max_votes_per_user" type="number" min="1" value="{{ old('max_votes_per_user', 1) }}" class="w-full border-gray-300 rounded-md px-3 py-2">
                    </div>

                    <div class="flex items-center">
                        <input id="is_featured" name="is_featured" type="checkbox" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 text-gray-700">Feature this contest</label>
                    </div>
                </div>

                <div class="flex items-center gap-6 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <span class="ml-2 text-gray-700">Activate contest</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <span class="ml-2 text-gray-700">Require admin approval for nominees/votes</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('voting.index') }}" class="mr-3 inline-block px-4 py-2 border rounded-md text-gray-700 bg-gray-100">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">Create Contest</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
