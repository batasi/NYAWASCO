<template>
    <div class="relative" ref="searchContainer">
        <div class="relative">
            <input
                v-model="searchQuery"
                @focus="showResults = true"
                @input="performSearch"
                type="text"
                placeholder="Search events, votes, organizers..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Search Results Dropdown -->
        <div v-if="showResults && searchQuery.length > 0"
             class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">

            <!-- Loading State -->
            <div v-if="loading" class="p-4 text-center text-gray-500">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-2">Searching...</p>
            </div>

            <!-- Results -->
            <div v-else>
                <!-- Events Results -->
                <div v-if="results.events.length > 0" class="border-b border-gray-100">
                    <div class="px-4 py-2 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Events
                    </div>
                    <div v-for="event in results.events" :key="'event-' + event.id"
                         class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-l-4 border-blue-500">
                        <div class="font-medium text-gray-900">{{ event.name }}</div>
                        <div class="text-sm text-gray-600 flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ event.location }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ formatDate(event.date) }}</div>
                    </div>
                </div>

                <!-- Voting Results -->
                <div v-if="results.voting.length > 0" class="border-b border-gray-100">
                    <div class="px-4 py-2 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Voting Contests
                    </div>
                    <div v-for="vote in results.voting" :key="'vote-' + vote.id"
                         class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-l-4 border-purple-500">
                        <div class="font-medium text-gray-900">{{ vote.title }}</div>
                        <div class="text-sm text-gray-600 flex items-center mt-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="vote.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                {{ vote.status === 'active' ? 'Live' : 'Ended' }}
                            </span>
                            <span class="ml-2">{{ vote.votes_count }} votes</span>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div v-if="!loading && results.events.length === 0 && results.voting.length === 0"
                     class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2">No results found for "{{ searchQuery }}"</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            searchQuery: '',
            showResults: false,
            loading: false,
            results: {
                events: [],
                voting: []
            },
            searchTimeout: null
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
        clearTimeout(this.searchTimeout);
    },
    methods: {
        performSearch() {
            clearTimeout(this.searchTimeout);

            if (this.searchQuery.length < 2) {
                this.results = { events: [], voting: [] };
                return;
            }

            this.loading = true;

            this.searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/search?q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await response.json();
                    this.results = data;
                } catch (error) {
                    console.error('Search error:', error);
                    // Fallback demo data
                    this.results = {
                        events: [
                            {
                                id: 1,
                                name: 'Nairobi Jazz Festival 2024',
                                location: 'Kenyatta International Conference Centre',
                                date: '2024-03-15'
                            },
                            {
                                id: 2,
                                name: 'Tech Innovation Summit',
                                location: 'Sarit Centre, Nairobi',
                                date: '2024-04-20'
                            }
                        ],
                        voting: [
                            {
                                id: 1,
                                title: 'Mr. & Miss Mombasa Awards 2025',
                                status: 'active',
                                votes_count: 1247
                            }
                        ]
                    };
                } finally {
                    this.loading = false;
                }
            }, 300);
        },
        handleClickOutside(event) {
            if (!this.$refs.searchContainer.contains(event.target)) {
                this.showResults = false;
            }
        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
    }
}
</script>
