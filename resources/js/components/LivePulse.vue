<template>
    <div class="fixed right-4 top-1/2 transform -translate-y-1/2 z-50 hidden lg:block">
        <div class="bg-white rounded-2xl shadow-2xl p-4 w-64 max-h-96 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Live Pulse</h3>
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            </div>

            <div class="space-y-3">
                <div v-for="(activity, index) in activities"
                     :key="index"
                     class="flex items-start p-2 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm mr-3 mt-1"
                         :class="getActivityIcon(activity.type).bg">
                        {{ getActivityIcon(activity.type).emoji }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700" v-html="activity.message"></p>
                        <p class="text-xs text-gray-500">{{ formatTime(activity.timestamp) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-200">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>{{ totalActivities }} activities today</span>
                    <button @click="showAllActivities = !showAllActivities" class="text-blue-600 hover:text-blue-800">
                        {{ showAllActivities ? 'Show Less' : 'Show More' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            activities: [],
            totalActivities: 0,
            showAllActivities: false,
            activityTypes: {
                ticket: { emoji: '💫', bg: 'bg-blue-500' },
                vote: { emoji: '🗳️', bg: 'bg-purple-500' },
                trend: { emoji: '🔥', bg: 'bg-red-500' },
                user: { emoji: '👤', bg: 'bg-green-500' }
            }
        }
    },
    mounted() {
        this.fetchLiveActivities();
        // Simulate real-time updates
        this.interval = setInterval(this.fetchLiveActivities, 10000);
    },
    beforeUnmount() {
        clearInterval(this.interval);
    },
    methods: {
        async fetchLiveActivities() {
            try {
                const response = await fetch('/api/live-activities');
                const data = await response.json();
                this.activities = data.activities;
                this.totalActivities = data.total;
            } catch (error) {
                console.error('Error fetching live activities:', error);
                // Fallback demo data
                this.activities = [
                    {
                        message: '<strong>Aisha J.</strong> just booked a ticket for <strong>Nairobi Jazz Fest</strong>',
                        type: 'ticket',
                        timestamp: new Date()
                    },
                    {
                        message: 'A vote was just cast for <strong>Mr. Mombasa 2025</strong>',
                        type: 'vote',
                        timestamp: new Date(Date.now() - 2 * 60000)
                    },
                    {
                        message: '<strong>Tech Conference 2025</strong> is trending with 50+ bookings today',
                        type: 'trend',
                        timestamp: new Date(Date.now() - 5 * 60000)
                    }
                ];
                this.totalActivities = 142;
            }
        },
        getActivityIcon(type) {
            return this.activityTypes[type] || { emoji: '🔔', bg: 'bg-gray-500' };
        },
        formatTime(timestamp) {
            const now = new Date();
            const activityTime = new Date(timestamp);
            const diffMinutes = Math.floor((now - activityTime) / 60000);

            if (diffMinutes < 1) return 'Just now';
            if (diffMinutes < 60) return `${diffMinutes}m ago`;
            if (diffMinutes < 1440) return `${Math.floor(diffMinutes / 60)}h ago`;
            return `${Math.floor(diffMinutes / 1440)}d ago`;
        }
    }
}
</script>
