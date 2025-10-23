<div class="content-slideshow" 
     x-data="{
         currentEventSlide: 0, 
         totalEventSlides: {{ $events->count() }}, 
         eventAutoSlide: true,
         eventInterval: null
     }"
     x-init="
         eventInterval = setInterval(() => {
             if (eventAutoSlide && totalEventSlides > 0) {
                 currentEventSlide = (currentEventSlide + 1) % totalEventSlides;
             }
         }, 4000);
     ">
    
    @forelse($events as $index => $event)
    @php
        $backgroundStyle = $event->image_url 
            ? "linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{$event->image_url}')"
            : "linear-gradient(135deg, rgba(236,72,153,0.9), rgba(0,0,0,0.8))";
    @endphp

    <div class="content-slide events" 
         :class="{ 'active': currentEventSlide === {{ $index }} }"
         style="background: {{ $backgroundStyle }}; background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="slide-content-wrapper">
            <div class="slide-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h4 class="slide-title">{{ $event->name }}</h4>
            <p class="slide-meta">
                @if($event->start_date)
                    {{ $event->start_date->format('M d, Y') }}
                @else
                    Date TBA
                @endif
                @if($event->end_date && $event->end_date != $event->start_date)
                    <br>to {{ $event->end_date->format('M d, Y') }}
                @endif
                <br>
                @if($event->location)
                    {{ $event->location }}
                @endif
            </p>
            <a href="{{ route('events.show', $event->id) }}" class="slide-badge-small">Event</a>
        </div>
    </div>
    @empty
    <div class="slideshow-empty">
        <div class="empty-content">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <p class="text-sm">No upcoming events</p>
        </div>
    </div>
    @endforelse
    
    <!-- Event Slide Indicators -->
    @if($events->count() > 1)
    <div class="content-slide-indicators">
        @foreach($events as $index => $event)
        <div :class="{ 'active': currentEventSlide === {{ $index }} }" 
             class="content-slide-indicator"
             @click="currentEventSlide = {{ $index }}; eventAutoSlide = false; setTimeout(() => eventAutoSlide = true, 8000)"></div>
        @endforeach
    </div>
    @endif
</div>