
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <div x-data="dashboardMenu()" class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo + Title -->
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">J</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-500">Manage all activities in the system from a central position</p>
                </div>
            </div>

            <!-- Desktop Buttons (md+) -->
            <div class="hidden md:flex items-center gap-2 justify-end">
                <template x-for="button in buttons" :key="button.name">
                    <div x-data="{ open: false }" class="relative">
                        <!-- Desktop Button -->
                        <template x-if="!button.dropdown">
                            <a :href="button.href"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white"
                            :class="button.type === 'primary'
                                        ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                                <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                                <span x-text="button.name"></span>
                            </a>
                        </template>

                        <!-- Dropdown Button -->
                        <template x-if="button.dropdown">
                            <a href="#" @mouseenter="open = true" @mouseleave="open = false"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800">
                                <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                                <span x-text="button.name"></span>
                                <i class="fas fa-chevron-down ml-1"></i>
                            </a>
                        </template>

                        <!-- Dropdown Menu -->
                        <div x-show="open && button.dropdown" @mouseenter="open=true" @mouseleave="open=false"
                            class="absolute mt-2 w-48 bg-white border rounded shadow z-10">
                            <template x-for="link in button.links" :key="link.name">
                                <a :href="link.href" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" x-text="link.name"></a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Mobile Toggle Button -->
            <div class="mobile-toggle">
                <button @click="mobileMenuOpen = true" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg">
                    <i class="fas fa-bars"></i>
                </button>
                </div>

                <style>
                /* Hide toggle at ≥ 768px (md) */
                @media (min-width: 768px) {
                    .mobile-toggle { display: none !important; }
                }
                </style>
        </div>

        <!-- Mobile Sidebar -->
        <div x-show="mobileMenuOpen" x-transition x-cloak class="fixed inset-0 z-50 flex">
            <!-- Overlay -->
            <div class="bg-black bg-opacity-50 w-full" @click="mobileMenuOpen = false"></div>

            <!-- Sidebar -->
            <div class="bg-blue-600 w-64 p-4 flex flex-col space-y-2 overflow-y-auto text-white fixed left-0 top-0 h-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-lg">Menu</h2>
                    <button @click="mobileMenuOpen = false" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <template x-for="button in buttons" :key="button.name">
                    <div x-data="{ open: false }">
                        <a href="#" @click="button.dropdown ? open = !open : mobileMenuOpen=false"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                            <span x-text="button.name"></span>
                            <template x-if="button.dropdown">
                                <i class="fas fa-chevron-down ml-auto"></i>
                            </template>
                        </a>

                        <!-- Dropdown links -->
                        <div x-show="open && button.dropdown" class="pl-6 mt-1 space-y-1">
                            <template x-for="link in button.links" :key="link.name">
                                <a :href="link.href" @click="mobileMenuOpen=false"
                                class="block px-4 py-2 rounded hover:bg-blue-700 text-white"
                                x-text="link.name"></a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function dashboardMenu() {
            return {
                mobileMenuOpen: false,
                buttons: [
                    { name: 'Meters', href: '{{ route("admin.meters.index") }}', type: 'primary', icon: 'fas fa-tachometer-alt', dropdown: false },
                    { name: 'Billings', href: '{{ route("bills.index") }}', type: 'primary', icon: 'fas fa-file-invoice-dollar', dropdown: false },
                    { name: 'Payments', href: '{{ route("payments.index") }}', type: 'primary', icon: 'fas fa-credit-card', dropdown: false },
                    {
                        name: 'Customers',
                        href: '#',
                        type: 'primary',
                        icon: 'fas fa-users',
                        dropdown: true,
                        links: [
                            { name: 'All Customers', href: '{{ route("admin.customers.index") }}' },
                            { name: 'Register Customer', href: '{{ route("water-connection") }}' },
                        ]
                    },
                    { name: 'Reports', href: '#', type: 'primary', icon: 'fas fa-chart-line', dropdown: false },
                ]
            }
        }
    </script>
