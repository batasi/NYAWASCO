@props([
    'title' => 'Page Title',
    'subtitle' => '',
    'actionButtons' => []
])

<!-- Alpine.js Menu System -->
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="dashboardMenu()" class="bg-white shadow-sm border-b border-gray-200 bg-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">

        <!-- Logo + Page Titles -->
        <div class="flex items-center space-x-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
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
            @auth
            <div class="relative hidden md:flex" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center px-3 py-2 rounded-lg bg-red-200 hover:bg-gray-200">
                    <i class="fas fa-user-circle text-xl mr-2"></i>
                    <span>Acc</span>
                    <i class="fas fa-chevron-down ml-1" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" x-transition x-cloak
                    class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg overflow-hidden z-50">
                    <a href="#"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="open = false">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                @click="open = false">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
            @endauth


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

            <!-- Mobile Menu Toggle -->
            <div class="mobile-toggle">
                <button @click="mobileMenuOpen = true" class="mobile-menu-btn">
                    <i class="fas fa-bars text-blue-800"></i>
                </button>
            </div>
            <style>
                @media(min-width:768px){
                    .mobile-menu-btn{ display:none !important; }
                }
            </style>
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

            @auth
            <!-- Profile / Logout at bottom -->
            <div class="mt-auto border-t pt-2">
                <a href="#"
                   class="mobile-slide-link"
                   @click="mobileMenuOpen = false">
                    <i class="fas fa-user-circle"></i> Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mobile-slide-link w-full text-left" @click="mobileMenuOpen = false">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </button>
                </form>
            </div>
            @endauth

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
            @can('view meters')
            { name: 'Meters', href: '{{ route("admin.meters.index") }}', type: 'primary', icon: 'fas fa-tachometer-alt' },
            @endcan
            @can('view bills')
            { name: 'Billings', href: '{{ route("bills.index") }}', type: 'primary', icon: 'fas fa-file-invoice' },
            @endcan
            @can('view payments')
            { name: 'Payments', href: '{{ route("payments.index") }}', type: 'primary', icon: 'fas fa-credit-card' },
            @endcan
            @can('view customers')
            { name: 'Customers', href: '{{ route("admin.customers.index") }}', type: 'primary', icon: 'fas fa-users' },
            @endcan
        ],
    }
}
</script>
