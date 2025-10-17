@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('voting.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    <span class="sr-only">Back to Voting</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('voting.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Voting Contests</a>
                            </li>
                            <li>
                                <span class="text-gray-300">/</span>
                            </li>
                            <li>
                                <span class="text-sm font-medium text-gray-900">{{ $contest->title }}</span>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $contest->title }}</h1>
                    <p class="mt-2 text-lg text-gray-600">Organized by {{ $contest->organizer->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $contest->isOngoing() ? 'bg-green-100 text-green-800' :
                           ($contest->hasEnded() ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $contest->isOngoing() ? 'Live Voting' : ($contest->hasEnded() ? 'Voting Ended' : 'Upcoming') }}
                    </span>
                    @if($contest->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Featured
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Contest Image and Description -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    @if($contest->featured_image)
                        <img src="{{ Storage::url($contest->featured_image) }}"
                             alt="{{ $contest->title }}"
                             class="w-full h-64 object-cover rounded-lg mb-6">
                    @endif

                    <div class="prose prose-purple max-w-none">
                        <p class="text-gray-700 text-lg leading-relaxed">{{ $contest->description }}</p>
                    </div>

                    @if($contest->rules)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Voting Rules</h3>
                            <div class="prose prose-sm text-gray-600">
                                {!! nl2br(e($contest->rules)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Nominees Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Nominees</h2>

                    @if($contest->nominees->count() > 0)
                        <div class="space-y-6">
                            @foreach($contest->nominees as $nominee)
                                <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:border-purple-300 transition-colors duration-200">
                                    <!-- Nominee Photo -->
                                    <div class="flex-shrink-0">
                                        @if($nominee->photo)
                                            <img src="{{ Storage::url($nominee->photo) }}"
                                                 alt="{{ $nominee->name }}"
                                                 class="w-16 h-16 rounded-full object-cover">
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-purple-500 flex items-center justify-center text-white text-xl font-bold">
                                                {{ strtoupper(substr($nominee->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Nominee Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $nominee->name }}</h3>
                                        @if($nominee->bio)
                                            <p class="text-gray-600 mt-1">{{ $nominee->bio }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            <span>{{ $nominee->votes_count }} votes</span>
                                            @if($contest->total_votes > 0)
                                                <span>{{ number_format($nominee->vote_percentage, 1) }}%</span>
                                            @endif
                                        </div>

                                        <!-- Vote Progress Bar -->
                                        @if($contest->total_votes > 0)
                                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full"
                                                     style="width: {{ $nominee->vote_percentage }}%"></div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Vote Button -->
                                    @if($contest->isOngoing() && auth()->check() && $contest->userCanVote(auth()->user()) && !$userVote)
                                        <div class="flex-shrink-0">
                                            <form action="{{ route('voting.vote', $contest) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="nominee_id" value="{{ $nominee->id }}">
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    Vote
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <!-- User's Vote Indicator -->
                                    @if($userVote && $userVote->nominee_id == $nominee->id)
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Your Vote
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No nominees yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Nominees will be added soon.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contest Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contest Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Started: {{ $contest->start_date->format('M j, Y g:i A') }}</span>
                        </div>
                        @if($contest->end_date)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Ends: {{ $contest->end_date->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Total Votes: {{ $contest->total_votes }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Votes per user: {{ $contest->max_votes_per_user }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Category: {{ $contest->category->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Voting Instructions -->
                @if($contest->isOngoing())
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">How to Vote</h3>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Browse the nominees below</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Click the "Vote" button for your choice</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>You can vote for up to {{ $contest->max_votes_per_user }} nominee(s)</span>
                            </li>
                        </ul>
                    </div>
                @endif

                <!-- Share Contest -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Share this Contest</h3>
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors">
                            Facebook
                        </button>
                        <button class="flex-1 bg-blue-400 text-white px-3 py-2 rounded text-sm hover:bg-blue-500 transition-colors">
                            Twitter
                        </button>
                        <button class="flex-1 bg-gray-800 text-white px-3 py-2 rounded text-sm hover:bg-gray-900 transition-colors">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
