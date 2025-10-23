<div class="content-slideshow" 
     x-data="{
         currentVotingSlide: 0, 
         totalVotingSlides: {{ $contests->count() }}, 
         votingAutoSlide: true,
         votingInterval: null
     }"
     x-init="
         votingInterval = setInterval(() => {
             if (votingAutoSlide && totalVotingSlides > 0) {
                 currentVotingSlide = (currentVotingSlide + 1) % totalVotingSlides;
             }
         }, 4500);
     ">
    
    @forelse($contests as $index => $contest)
    @php
        $backgroundStyle = $contest->image_url 
            ? "linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{$contest->image_url}')"
            : "linear-gradient(135deg, rgba(0,0,0,0.9), rgba(236,72,153,0.8))";
    @endphp

    <div class="content-slide voting" 
         :class="{ 'active': currentVotingSlide === {{ $index }} }"
         style="background: {{ $backgroundStyle }}; background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="slide-content-wrapper">
            <div class="slide-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h4 class="slide-title">{{ $contest->title }}</h4>
            <p class="slide-meta">
                {{ $contest->nominees->count() }} nominees
                <br>
                @if($contest->start_date)
                    Started {{ $contest->start_date->format('M d, Y') }}
                @else
                    Starting Soon
                @endif
                <br>
                @if($contest->end_date)
                    Ends {{ $contest->end_date->format('M d, Y') }}
                @else
                    Ongoing
                @endif
            </p>
            <a href="{{ route('voting.show', $contest->id) }}" class="slide-badge-small">Vote Now</a>
        </div>
    </div>
    @empty
    <div class="slideshow-empty">
        <div class="empty-content">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm">No active contests</p>
        </div>
    </div>
    @endforelse
    
    <!-- Voting Slide Indicators -->
    @if($contests->count() > 1)
    <div class="content-slide-indicators">
        @foreach($contests as $index => $contest)
        <div :class="{ 'active': currentVotingSlide === {{ $index }} }" 
             class="content-slide-indicator"
             @click="currentVotingSlide = {{ $index }}; votingAutoSlide = false; setTimeout(() => votingAutoSlide = true, 8000)"></div>
        @endforeach
    </div>
    @endif
</div>