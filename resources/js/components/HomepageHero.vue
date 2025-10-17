<template>
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Personalized Welcome for Logged-in Users -->
            <div v-if="userLoggedIn" class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Welcome back, <span class="text-yellow-300">{{ userName }}</span>!
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Ready for your next adventure? Here's what's happening in your EventSphere.
                </p>
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 max-w-2xl mx-auto">
                    <div class="grid md:grid-cols-2 gap-4 text-left">
                        <div class="flex items-center p-4 bg-white bg-opacity-5 rounded-lg">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-200">Your Next Event</p>
                                <p class="font-semibold">Nairobi Jazz Fest</p>
                                <p class="text-xs text-blue-300">in 3 days</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-white bg-opacity-5 rounded-lg">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-200">Recent Vote</p>
                                <p class="font-semibold">Mr. & Miss Mombasa</p>
                                <p class="text-xs text-blue-300">Results coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <a href="/dashboard" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition duration-300">
                        Go to My Dashboard
                    </a>
                </div>
            </div>

            <!-- Split CTA for Logged-out Users -->
            <div v-else class="grid md:grid-cols-2 gap-8 items-center">
                <!-- Ticketing Side -->
                <div class="text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Discover Your Next
                        <span class="block text-yellow-300">Experience</span>
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        Find and book tickets for the best events in your city. From concerts to conferences, your next adventure awaits.
                    </p>
                    <a href="/events" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition duration-300 inline-flex items-center">
                        Browse Events & Tickets
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Voting Side -->
                <div class="text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Shape the
                        <span class="block text-pink-300">Outcome</span>
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        Participate in live voting for awards, contests, and community decisions. Your voice matters.
                    </p>
                    <a href="/voting" class="bg-white text-pink-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition duration-300 inline-flex items-center">
                        View Active Votes
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    props: {
        userLoggedIn: {
            type: Boolean,
            required: true
        },
        userName: {
            type: String,
            default: ''
        }
    },
    mounted() {
        console.log('HomepageHero component mounted');
    }
}
</script>
