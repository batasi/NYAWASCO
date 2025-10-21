@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
<!-- Contest Header -->
<div 
    class="relative bg-gray-900 text-white"
    @if($contest->featured_image)
        style="background-image: url('{{ Storage::url($contest->featured_image) }}'); background-size: cover; background-position: center;"
    @endif
>
    <div class="absolute inset-0 bg-yellow bg-opacity-60"></div> <!-- dark overlay -->

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-6 lg:space-y-0">
            <div class="flex-1">
                <!-- Breadcrumb -->
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-3 text-sm text-gray-300">
                        <li>
                            <a href="{{ route('voting.index') }}" class="hover:text-white flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back
                            </a>
                        </li>
                        <li>/</li>
                        <li>
                            <a href="{{ route('voting.index') }}" class="hover:text-white">Voting Contests</a>
                        </li>
                        <li>/</li>
                        <li class="text-gray-100 font-semibold">{{ $contest->title }}</li>
                    </ol>
                </nav>

                <!-- Contest Info -->
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">{{ $contest->title }}</h1>
                <p class="text-gray-200 text-lg leading-relaxed mb-4 max-w-2xl">
                    {{ $contest->description }}
                </p>

                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $contest->isOngoing() ? 'bg-green-600/80' :
                           ($contest->hasEnded() ? 'bg-gray-500/80' : 'bg-blue-600/80') }}">
                        {{ $contest->isOngoing() ? 'Live Voting' : ($contest->hasEnded() ? 'Voting Ended' : 'Upcoming') }}
                    </span>

                    @if($contest->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/90 text-gray-900">
                            Featured
                        </span>
                    @endif
                </div>

                <!-- Date Info -->
                <div class="mt-6 flex flex-wrap items-center text-sm text-gray-300 gap-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Starts: {{ $contest->start_date->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($contest->end_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Ends: {{ $contest->end_date->format('M j, Y g:i A') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Countdown -->
                <div class="mt-4">
                    <div id="countdown" class="text-lg font-semibold text-yellow-300 bg-black/40 inline-block px-4 py-2 rounded-md"></div>
                </div>
            </div>

            <!-- Rules Box -->
            @if($contest->rules)
                <div class="bg-white/10 border border-white/20 backdrop-blur-sm rounded-lg p-6 max-w-sm text-white">
                    <h3 class="text-lg font-semibold mb-3">Voting Rules</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{!! nl2br(e($contest->rules)) !!}</span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

    <script>
        // Countdown Timer Logic
        document.addEventListener('DOMContentLoaded', () => {
            const countdownElement = document.getElementById('countdown');
            const startTime = new Date("{{ $contest->start_date }}").getTime();
            const endTime = new Date("{{ $contest->end_date }}").getTime();
            const now = new Date().getTime();

            let targetTime, label;
            if (now < startTime) {
                targetTime = startTime;
                label = "Starts in:";
            } else if (now >= startTime && now < endTime) {
                targetTime = endTime;
                label = "Ends in:";
            } else {
                countdownElement.textContent = "Voting has ended";
                return;
            }

            const updateCountdown = () => {
                const current = new Date().getTime();
                const distance = targetTime - current;

                if (distance <= 0) {
                    countdownElement.textContent = (label === "Starts in:") ? "Voting is now LIVE!" : "Voting has ended";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownElement.textContent = `${label} ${days}d ${hours}h ${minutes}m ${seconds}s`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-4">
                <div class="rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Nominees</h2>

                    @if($contest->nominees->count() > 0)
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($contest->nominees as $nominee)
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-5 flex flex-col justify-between">
                                    <div>
                                        <!-- Photo -->
                                        <div class="flex justify-center mb-4">
                                            @if($nominee->photo)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($nominee->photo) }}"
                                                    alt="{{ $nominee->name }}"
                                                    class="w-24 h-24 rounded-full object-cover ring-4 ring-purple-100">
                                            @else
                                                <div class="w-24 h-24 rounded-full bg-purple-500 flex items-center justify-center text-white text-3xl font-bold">
                                                    {{ strtoupper(substr($nominee->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $nominee->name }}</h3>
                                            @if($nominee->bio)
                                                <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $nominee->bio }}</p>
                                            @endif

                                            <div class="mt-3 flex justify-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $nominee->votes_count }} votes</span>
                                                @if($contest->total_votes > 0)
                                                    <span>{{ number_format($nominee->vote_percentage, 1) }}%</span>
                                                @endif
                                            </div>

                                            @if($contest->total_votes > 0)
                                                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-purple-600 h-2 rounded-full"
                                                        style="width: {{ $nominee->vote_percentage }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="mt-5 text-center">
                                        @if($contest->isOngoing() && auth()->check() && $contest->userCanVote(auth()->user()) && !$userVote)
                                            <form action="{{ route('voting.vote', $contest) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="nominee_id" value="{{ $nominee->id }}">
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    Vote
                                                </button>
                                            </form>
                                        @endif

                                        @if($userVote && $userVote->nominee_id == $nominee->id)
                                            <span class="inline-flex items-center justify-center w-full mt-2 px-3 py-1 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Your Vote
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No nominees yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Nominees will be added soon.</p>
                        </div>
                    @endif
                </div>
            </div>

        
        </div>
    </div>
</div>
@endsection
