@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen modal-header">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white-900">
                        Active Voting Contests
                        @if(isset($category))
                            <span class="text-purple-600">in {{ $category->name }}</span>
                        @endif
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        Participate in live voting and help shape the outcomes
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    @auth
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.voting.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Voting Contest
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 modal-bg">
        <div class="modal-header rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-black mb-2">Search Contests</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" placeholder="Search by contest title, description..."
                               class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-4 pr-12 py-3 text-sm border-gray-300 rounded-md" style="color: black;">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-black mb-2">Category</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md" style="color: black;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ isset($category) && $category->id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-2">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md" style="color: black;">
                        <option value="all">All Contests</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="ended">Ended</option>
                    </select>
                </div>
            </div>

            <!-- Apply Filters Button -->
            <div class="mt-4 flex justify-end">
                <button type="button" id="applyFilters" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 text-sm font-medium">
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Voting Contests Grid -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-100">
                    @if(isset($category))
                        {{ $category->name }} Voting
                    @else
                        All Voting Contests
                    @endif
                    <span class="text-lg text-purple-400">({{ $contests->total() }} found)</span>
                </h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-400">Sort by:</span>
                    <select id="sort" name="sort" class="text-sm border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" style="color: black;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="ending_soon">Ending Soon</option>
                        <option value="most_votes">Most Votes</option>
                        <option value="featured">Featured</option>
                    </select>
                </div>
            </div>

            @if($contests->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($contests as $contest)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Contest Image -->
                            <div class="relative h-48 bg-gradient-to-br from-purple-400 to-pink-500">
                                @if($contest->featured_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($contest->featured_image) }}"
                                         alt="{{ $contest->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white">
                                        <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badges -->
                                <div class="absolute top-3 left-3 flex flex-col space-y-2">
                                    @if($contest->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Featured
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $contest->isOngoing() ? 'bg-green-100 text-green-800' :
                                           ($contest->hasEnded() ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $contest->isOngoing() ? 'Live' : ($contest->hasEnded() ? 'Ended' : 'Upcoming') }}
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $contest->category->name }}
                                    </span>
                                </div>

                                <!-- Vote Count -->
                                <div class="absolute bottom-3 left-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-90 text-gray-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $contest->total_votes }} votes
                                    </span>
                                </div>
                            </div>

                            <!-- Contest Content -->
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                        <a href="{{ route('voting.show', $contest) }}" class="hover:text-purple-600">
                                            {{ $contest->title }}
                                        </a>
                                    </h3>
                                </div>

                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($contest->description, 120) }}
                                </p>

                                <!-- Contest Details -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        @if($contest->end_date)
                                            Ends {{ $contest->end_date->diffForHumans() }}
                                        @else
                                            No end date
                                        @endif
                                    </div>
                                   
                                </div>

                                <!-- Nominees Preview -->
                                @if($contest->nominees->count() > 0)
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 mb-2">Nominees:</p>
                                        <div class="flex -space-x-2 overflow-hidden">
                                            @foreach($contest->nominees->take(4) as $nominee)
                                                <div class="relative group">
                                                    @if($nominee->photo)
                                                        <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                                             src="{{ \Illuminate\Support\Facades\Storage::url($nominee->photo) }}"
                                                             alt="{{ $nominee->name }}">
                                                    @else
                                                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                            {{ strtoupper(substr($nominee->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                                                        {{ $nominee->name }}
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($contest->nominees->count() > 4)
                                                <div class="inline-flex h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 items-center justify-center text-gray-600 text-xs font-bold">
                                                    +{{ $contest->nominees->count() - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Button -->
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">
                                        {{ $contest->max_votes_per_user }} vote{{ $contest->max_votes_per_user > 1 ? 's' : '' }} per user
                                    </div>
                                    <a href="{{ route('voting.show', $contest) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        @if($contest->isOngoing())
                                            Vote Now
                                        @elseif($contest->hasEnded())
                                            View Results
                                        @else
                                            View Details
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $contests->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No voting contests found</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        @if(isset($category))
                            There are no {{ $category->name }} voting contests matching your criteria.
                        @else
                            There are no voting contests matching your criteria. Try adjusting your filters.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('voting.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Clear Filters
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Categories Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Browse by Category</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $cat)
                    <a href="{{ route('voting.byCategory', $cat) }}"
                       class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors duration-200 group">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-2 group-hover:bg-purple-200">
                            @if($cat->icon)
                                <span class="text-lg">{{ $cat->icon }}</span>
                            @else
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-700 text-center group-hover:text-purple-700">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const applyFilters = document.getElementById('applyFilters');
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const statusSelect = document.getElementById('status');
    const sortSelect = document.getElementById('sort');

    function applyVotingFilters() {
        const params = new URLSearchParams();

        if (searchInput.value) params.append('search', searchInput.value);
        if (categorySelect.value) params.append('category', categorySelect.value);
        if (statusSelect.value !== 'all') params.append('status', statusSelect.value);
        if (sortSelect.value !== 'newest') params.append('sort', sortSelect.value);

        window.location.href = '{{ route('voting.index') }}?' + params.toString();
    }

    applyFilters.addEventListener('click', applyVotingFilters);

    // Enter key support for search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyVotingFilters();
        }
    });

    // Auto-apply sort
    sortSelect.addEventListener('change', applyVotingFilters);
});
</script>

<style>
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>
@endsection
