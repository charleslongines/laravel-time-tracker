<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage();
const isLoading = ref(false);

// Get flash messages from the session
const flash = computed(() => (page.props as any).flash || {});
const errors = computed(() => (page.props as any).errors || {});
const hasErrors = computed(() => Object.keys(errors.value).length > 0);
const hasSuccess = computed(() => flash.value.success);

// Clock in/out functionality
const clockIn = async () => {
    isLoading.value = true;

    try {
        // Get client information
        const userAgent = navigator.userAgent;

        // Get IP address from a public API
        let ipAddress = 'Unknown';
        try {
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            ipAddress = data.ip;
        } catch (error) {
            console.warn('Could not fetch IP address:', error);
        }

        router.post(
            '/time-tracker/clock-in',
            {
                userAgent: userAgent,
                ipAddress: ipAddress,
            },
            {
                onFinish: () => {
                    isLoading.value = false;
                },
            },
        );
    } catch (error) {
        console.error('Error during clock in:', error);
        isLoading.value = false;
    }
};

const clockOut = async () => {
    isLoading.value = true;

    try {
        // Get client information
        const userAgent = navigator.userAgent;

        // Get IP address from a public API
        let ipAddress = 'Unknown';
        try {
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            ipAddress = data.ip;
        } catch (error) {
            console.warn('Could not fetch IP address:', error);
        }

        router.post(
            '/time-tracker/clock-out',
            {
                userAgent: userAgent,
                ipAddress: ipAddress,
            },
            {
                onFinish: () => {
                    isLoading.value = false;
                },
            },
        );
    } catch (error) {
        console.error('Error during clock out:', error);
        isLoading.value = false;
    }
};

// Get time tracking data
const timeTrackers = computed(() => (page.props as any).timeTrackers || []);

// Format duration from seconds to readable format
const formatDuration = (duration: string | number) => {
    if (!duration) return 'N/A';

    // If duration is already a formatted string (HH:MM:SS), parse and reformat it
    if (typeof duration === 'string') {
        const parts = duration.split(':');
        if (parts.length === 3) {
            const hours = parseInt(parts[0]);
            const minutes = parseInt(parts[1]);
            const seconds = parseInt(parts[2]);

            if (hours > 0) {
                return `${hours}h ${minutes}m ${seconds}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${seconds}s`;
            } else {
                return `${seconds}s`;
            }
        }
        // If it's not in HH:MM:SS format, return as is
        return duration;
    }

    // If duration is a number (seconds), format it as before
    const hours = Math.floor(duration / 3600);
    const minutes = Math.floor((duration % 3600) / 60);
    const remainingSeconds = duration % 60;

    if (hours > 0) {
        return `${hours}h ${minutes}m ${remainingSeconds}s`;
    } else if (minutes > 0) {
        return `${minutes}m ${remainingSeconds}s`;
    } else {
        return `${remainingSeconds}s`;
    }
};

// Format date to readable format
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Clock In/Out Buttons -->
            <div class="mb-6 flex flex-col gap-4">
                <!-- Success Message Display -->
                <div v-if="hasSuccess" class="rounded-lg bg-green-100 p-3 text-sm text-green-800 dark:bg-green-900 dark:text-green-200">
                    {{ flash.success }}
                </div>

                <!-- Error Message Display -->
                <div v-if="hasErrors" class="rounded-lg bg-red-100 p-3 text-sm text-red-800 dark:bg-red-900 dark:text-red-200">
                    {{ errors.message }}
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button
                        @click="clockIn"
                        :disabled="isLoading"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition-colors duration-200 hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg v-if="isLoading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                        {{ isLoading ? 'Processing...' : 'Clock In' }}
                    </button>
                    <button
                        @click="clockOut"
                        :disabled="isLoading"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition-colors duration-200 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg v-if="isLoading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ isLoading ? 'Processing...' : 'Clock Out' }}
                    </button>
                </div>
            </div>

            <!-- Time Tracking Records Table -->
            <div class="mb-6">
                <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Time Tracking Records</h2>
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-[800px] divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    Start Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    End Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            <tr v-if="timeTrackers.length === 0" class="text-center">
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">No time tracking records found</td>
                            </tr>
                            <tr v-for="record in timeTrackers" :key="record.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ formatDate(record.start_time) }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ formatDate(record.start_time) }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ record.end_time ? formatDate(record.end_time) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-white">
                                    {{ formatDuration(record.duration) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                            record.end_time
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        ]"
                                    >
                                        {{ record.status.toUpperCase() }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
