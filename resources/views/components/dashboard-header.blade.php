@props([
    'title' => 'Page Title',
    'subtitle' => '',
    'actionButtons' => []
])

<!-- Alpine.js Menu System -->
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="dashboardMenu()" class="shadow-sm border-b border-blue-700 bg-green-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">

        <!-- Logo + Page Titles -->
        <div class="flex items-center space-x-4">

            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-xs md:text-sm text-gray-500">{{ $subtitle }}</p>
            </div>
        </div>

        <!-- RIGHT SIDE SECTION -->
        <div class="flex items-center space-x-3">

            <!-- Desktop Navigation Buttons -->
            <div class="hidden md:flex items-center gap-2">
                <template x-for="button in buttons" :key="button.name">
                    <a :href="button.href"
                       class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium shadow-sm border"
                       :class="button.type === 'primary'
                            ? 'text-white bg-blue-600 hover:bg-blue-700 border-blue-700'
                            : 'text-gray-700 bg-white hover:bg-gray-100 border-gray-300'">
                        <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                        <span x-text="button.name"></span>
                    </a>
                </template>
            </div>

            <!-- Profile Dropdown (Desktop only) -->



            <!-- ACTION BUTTONS -->
            @foreach($actionButtons as $btn)
                @if(isset($btn['onclick']))
                    <button onclick="{{ $btn['onclick'] }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white
                        {{ $btn['color'] ?? 'bg-green-600 hover:bg-green-700' }} shadow">
                        @if(isset($btn['icon']))
                            <i class="{{ $btn['icon'] }} mr-2"></i>
                        @endif
                        {{ $btn['text'] }}
                    </button>
                @elseif(isset($btn['href']))
                    <a href="{{ $btn['href'] }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white
                        {{ $btn['color'] ?? 'bg-blue-600 hover:bg-blue-700' }} shadow">
                        @if(isset($btn['icon']))
                            <i class="{{ $btn['icon'] }} mr-2"></i>
                        @endif
                        {{ $btn['text'] }}
                    </a>
                @endif
            @endforeach


        </div>
    </div>

    <!-- Mobile Slide Menu -->
    <div class="mobile-slide-wrapper" :class="{ 'open': mobileMenuOpen }">

        <!-- Dark overlay -->
        <div class="overlay" @click="mobileMenuOpen = false"></div>

        <!-- Sliding Menu -->
        <div class="mobile-slide-menu">
            <div class="mobile-slide-header">
                <h2 class="font-bold">Menu</h2>
                <button @click="mobileMenuOpen = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <template x-for="button in buttons" :key="button.name">
                <a :href="button.href"
                   @click="mobileMenuOpen = false"
                   class="mobile-slide-link">
                    <i :class="button.icon"></i>
                    <span x-text="button.name"></span>
                </a>
            </template>



        </div>
    </div>
</div>

<style>
.mobile-slide-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    display: flex;
    z-index: 1000;
    pointer-events: none;
}
.mobile-slide-wrapper.open { pointer-events: auto; }

.overlay {
    flex: 1;
    background: rgba(0,0,0,0.55);
    opacity: 0;
    transition: opacity .3s ease-in-out;
}
.mobile-slide-wrapper.open .overlay { opacity: 1; }

.mobile-slide-menu {
    width: 260px;
    background: #fff;
    height: 100%;
    transform: translateX(100%);
    transition: transform .35s ease-in-out;
    padding: 1rem;
    display: flex;
    flex-direction: column;
}
.mobile-slide-wrapper.open .mobile-slide-menu { transform: translateX(0); }

.mobile-slide-header {
    display: flex;
    justify-content: space-between;
    padding-bottom: .75rem;
    border-bottom: 1px solid #ddd;
    margin-bottom: 1rem;
}

.mobile-slide-link {
    padding: .75rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    font-size: 15px;
    border-radius: 6px;
    color: #333;
}
.mobile-slide-link:hover { background: #f0f0f0; }

.rotate-180 { transform: rotate(180deg); }
[x-cloak] { display: none; }
</style>

<script>
function dashboardMenu() {
    return {
        mobileMenuOpen: false,
        buttons: [

        ],
    }
}
</script>
