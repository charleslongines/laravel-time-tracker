<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps(['user', 'timeTrackers']);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'User Time Tracker',
        href: '#',
    },
];

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
    <Head :title="`${user.name} - Time Tracker`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <!-- User Info Header -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>Role: {{ user.is_admin ? 'Admin' : 'User' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ timeTrackers.length }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Records</div>
                    </div>
                </div>
            </div>

            <!-- Time Tracking Records Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Time Tracking Records</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                    Start Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                    End Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70 bg-white dark:divide-sidebar-border dark:bg-gray-800">
                            <tr v-if="timeTrackers.length === 0" class="text-center">
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">No time tracking records found</td>
                            </tr>
                            <tr v-for="record in timeTrackers" :key="record.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
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

                <div v-if="timeTrackers.length === 0" class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No time tracking records</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user hasn't clocked in yet.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
