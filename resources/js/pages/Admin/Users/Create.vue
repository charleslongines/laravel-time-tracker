<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle, Eye, EyeOff, XCircle } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
    {
        title: 'Create User',
        href: '/admin/users/create',
    },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
    ip_address: '',
    user_agent: '',
});

// Password visibility toggle
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

// Form submission state
const hasAttemptedSubmit = ref(false);

// Auto-detect IP and User Agent
const detectedIP = ref('');
const detectedUserAgent = ref('');

onMounted(async () => {
    try {
        // Try to get IP address from a public API
        const ipResponse = await fetch('https://api.ipify.org?format=json');
        const ipData = await ipResponse.json();
        detectedIP.value = ipData.ip;
        form.ip_address = ipData.ip;
    } catch (error) {
        console.warn('Could not detect IP address:', error);
    }

    // Get user agent from browser
    detectedUserAgent.value = navigator.userAgent;
    form.user_agent = navigator.userAgent;
});

// Password strength validation
const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { score: 0, label: '', color: '', requirements: [] };

    const requirements = [
        { label: 'At least 8 characters', met: password.length >= 8 },
        { label: 'Contains uppercase letter', met: /[A-Z]/.test(password) },
        { label: 'Contains lowercase letter', met: /[a-z]/.test(password) },
        { label: 'Contains number', met: /\d/.test(password) },
        {
            label: 'Contains special character',
            met: /[!@#$%^&*(),.?":{}|<>]/.test(password),
        },
    ];

    const metCount = requirements.filter((req) => req.met).length;
    let score = 0;
    let label = '';
    let color = '';

    if (metCount <= 2) {
        score = 1;
        label = 'Weak';
        color = 'text-red-600 dark:text-red-400';
    } else if (metCount <= 3) {
        score = 2;
        label = 'Fair';
        color = 'text-yellow-600 dark:text-yellow-400';
    } else if (metCount <= 4) {
        score = 3;
        label = 'Good';
        color = 'text-blue-600 dark:text-blue-400';
    } else {
        score = 4;
        label = 'Strong';
        color = 'text-green-600 dark:text-green-400';
    }

    return { score, label, color, requirements };
});

// Password confirmation validation
const passwordMatch = computed(() => {
    if (!form.password_confirmation) return null;
    return form.password === form.password_confirmation;
});

// Form validation
const formErrors = computed(() => {
    const errors: Record<string, string> = {};

    if (!form.name.trim()) {
        errors.name = 'Name is required';
    } else if (form.name.length < 2) {
        errors.name = 'Name must be at least 2 characters';
    } else if (form.name.length > 100) {
        errors.name = 'Name must be less than 100 characters';
    }

    if (!form.email.trim()) {
        errors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address';
    }

    if (!form.password) {
        errors.password = 'Password is required';
    } else if (passwordStrength.value.score < 2) {
        errors.password = 'Password is too weak';
    }

    if (!form.password_confirmation) {
        errors.password_confirmation = 'Please confirm your password';
    } else if (!passwordMatch.value) {
        errors.password_confirmation = 'Passwords do not match';
    }

    // IP address validation (optional but if provided, should be valid)
    if (form.ip_address && !/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(form.ip_address)) {
        errors.ip_address = 'Please enter a valid IP address';
    }

    // User agent validation (optional but if provided, should not be empty)
    if (form.user_agent && form.user_agent.trim().length === 0) {
        errors.user_agent = 'User agent cannot be empty';
    }

    return errors;
});

const isFormValid = computed(() => {
    return Object.keys(formErrors.value).length === 0;
});

// Clear password confirmation error when password changes
watch(
    () => form.password,
    () => {
        if (form.errors.password_confirmation) {
            form.clearErrors('password_confirmation');
        }
    },
);

const submit = () => {
    hasAttemptedSubmit.value = true;

    if (!isFormValid.value) {
        return;
    }

    form.post(route('admin.users.store'), {
        onSuccess: () => {
            // Form will be reset automatically by Inertia
            hasAttemptedSubmit.value = false;
        },
        onError: () => {
            // Server-side errors will be handled automatically
        },
    });
};

// Helper function to truncate user agent for display
const truncateUserAgent = (userAgent: string, maxLength: number = 80) => {
    if (userAgent.length <= maxLength) return userAgent;
    return userAgent.substring(0, maxLength) + '...';
};
</script>

<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-xl border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-gray-800">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create New User</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new user to the system with appropriate permissions.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6 p-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autocomplete="name"
                            placeholder="Enter full name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500':
                                    (hasAttemptedSubmit || form.errors.name) && (formErrors.name || form.errors.name),
                                'border-green-500 focus:border-green-500 focus:ring-green-500': form.name && !form.errors.name && !formErrors.name,
                            }"
                        />
                        <p
                            v-if="(hasAttemptedSubmit || form.errors.name) && (formErrors.name || form.errors.name)"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.name || formErrors.name }}
                        </p>
                        <p
                            v-else-if="form.name && !form.errors.name && !formErrors.name"
                            class="mt-1 flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <CheckCircle class="mr-1 h-4 w-4" />
                            Name looks good
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="Enter email address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500':
                                    (hasAttemptedSubmit || form.errors.email) && (formErrors.email || form.errors.email),
                                'border-green-500 focus:border-green-500 focus:ring-green-500': form.email && !form.errors.email && !formErrors.email,
                            }"
                        />
                        <p
                            v-if="(hasAttemptedSubmit || form.errors.email) && (formErrors.email || form.errors.email)"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.email || formErrors.email }}
                        </p>
                        <p
                            v-else-if="form.email && !form.errors.email && !formErrors.email"
                            class="mt-1 flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <CheckCircle class="mr-1 h-4 w-4" />
                            Email format is valid
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Enter password"
                                class="mt-1 block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                :class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                        (hasAttemptedSubmit || form.errors.password) && (formErrors.password || form.errors.password),
                                    'border-green-500 focus:border-green-500 focus:ring-green-500':
                                        form.password && !form.errors.password && !formErrors.password && passwordStrength.score >= 3,
                                }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 mt-1 flex items-center pr-3"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <Eye v-if="!showPassword" class="h-4 w-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" />
                                <EyeOff v-else class="h-4 w-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" />
                            </button>
                        </div>

                        <!-- Password strength indicator -->
                        <div v-if="form.password" class="mt-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Password strength:</span>
                                <span :class="passwordStrength.color" class="font-medium">{{ passwordStrength.label }}</span>
                            </div>
                            <div class="mt-1 flex space-x-1">
                                <div
                                    v-for="i in 4"
                                    :key="i"
                                    class="h-1 flex-1 rounded-full"
                                    :class="{
                                        'bg-red-500': i <= passwordStrength.score && passwordStrength.score <= 2,
                                        'bg-yellow-500': i <= passwordStrength.score && passwordStrength.score === 3,
                                        'bg-green-500': i <= passwordStrength.score && passwordStrength.score === 4,
                                        'bg-gray-200 dark:bg-gray-600': i > passwordStrength.score,
                                    }"
                                ></div>
                            </div>

                            <!-- Password requirements -->
                            <div class="mt-2 space-y-1">
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Requirements:</p>
                                <div v-for="req in passwordStrength.requirements" :key="req.label" class="flex items-center text-xs">
                                    <CheckCircle v-if="req.met" class="mr-1 h-3 w-3 text-green-500" />
                                    <XCircle v-else class="mr-1 h-3 w-3 text-red-500" />
                                    <span :class="req.met ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                        {{ req.label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="(hasAttemptedSubmit || form.errors.password) && (formErrors.password || form.errors.password)"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.password || formErrors.password }}
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm your password"
                                class="mt-1 block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                :class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500':
                                        ((hasAttemptedSubmit || form.errors.password_confirmation) &&
                                            (formErrors.password_confirmation || form.errors.password_confirmation)) ||
                                        ((hasAttemptedSubmit || form.password_confirmation) && form.password_confirmation && !passwordMatch),
                                    'border-green-500 focus:border-green-500 focus:ring-green-500':
                                        form.password_confirmation && passwordMatch && !form.errors.password_confirmation,
                                }"
                            />
                            <button
                                type="button"
                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                                class="absolute inset-y-0 right-0 mt-1 flex items-center pr-3"
                                :aria-label="showPasswordConfirmation ? 'Hide password' : 'Show password'"
                            >
                                <Eye v-if="!showPasswordConfirmation" class="h-4 w-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" />
                                <EyeOff v-else class="h-4 w-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" />
                            </button>
                        </div>

                        <p
                            v-if="
                                (hasAttemptedSubmit || form.errors.password_confirmation) &&
                                (formErrors.password_confirmation || form.errors.password_confirmation)
                            "
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.password_confirmation || formErrors.password_confirmation }}
                        </p>
                        <p
                            v-else-if="(hasAttemptedSubmit || form.password_confirmation) && form.password_confirmation && !passwordMatch"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            Passwords do not match
                        </p>
                        <p
                            v-else-if="form.password_confirmation && passwordMatch"
                            class="mt-1 flex items-center text-sm text-green-600 dark:text-green-400"
                        >
                            <CheckCircle class="mr-1 h-4 w-4" />
                            Passwords match
                        </p>
                    </div>

                    <!-- Admin Role -->
                    <div class="flex items-start space-x-3">
                        <input
                            id="is_admin"
                            v-model="form.is_admin"
                            type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                        />
                        <div class="flex-1">
                            <label for="is_admin" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> Admin User </label>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Grant this user administrative privileges. Admin users can manage other users and access all system features.
                            </p>
                        </div>
                    </div>

                    <!-- IP Address -->
                    <div>
                        <label for="ip_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> IP Address </label>
                        <input
                            id="ip_address"
                            v-model="form.ip_address"
                            type="text"
                            placeholder="Enter IP address (auto-detected)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500':
                                    (hasAttemptedSubmit || form.errors.ip_address) && (formErrors.ip_address || form.errors.ip_address),
                                'border-green-500 focus:border-green-500 focus:ring-green-500':
                                    form.ip_address && !form.errors.ip_address && !formErrors.ip_address,
                            }"
                        />
                        <p
                            v-if="(hasAttemptedSubmit || form.errors.ip_address) && (formErrors.ip_address || form.errors.ip_address)"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.ip_address || formErrors.ip_address }}
                        </p>
                        <p
                            v-else-if="detectedIP && form.ip_address === detectedIP"
                            class="mt-1 flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <CheckCircle class="mr-1 h-4 w-4" />
                            IP address auto-detected
                        </p>
                    </div>

                    <!-- User Agent -->
                    <div>
                        <label for="user_agent" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> User Agent </label>
                        <textarea
                            id="user_agent"
                            v-model="form.user_agent"
                            rows="3"
                            placeholder="Enter user agent (auto-detected)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500':
                                    (hasAttemptedSubmit || form.errors.user_agent) && (formErrors.user_agent || form.errors.user_agent),
                                'border-green-500 focus:border-green-500 focus:ring-green-500':
                                    form.user_agent && !form.errors.user_agent && !formErrors.user_agent,
                            }"
                        ></textarea>
                        <p
                            v-if="(hasAttemptedSubmit || form.errors.user_agent) && (formErrors.user_agent || form.errors.user_agent)"
                            class="mt-1 flex items-center text-sm text-red-600 dark:text-red-400"
                        >
                            <XCircle class="mr-1 h-4 w-4" />
                            {{ form.errors.user_agent || formErrors.user_agent }}
                        </p>
                        <p
                            v-else-if="detectedUserAgent && form.user_agent === detectedUserAgent"
                            class="mt-1 flex items-center text-sm text-blue-600 dark:text-blue-400"
                        >
                            <CheckCircle class="mr-1 h-4 w-4" />
                            User agent auto-detected
                        </p>
                        <p v-if="form.user_agent" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Preview: {{ truncateUserAgent(form.user_agent) }}
                        </p>
                    </div>

                    <!-- Form validation summary -->
                    <div v-if="hasAttemptedSubmit && Object.keys(formErrors).length > 0" class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                        <div class="flex">
                            <AlertCircle class="h-5 w-5 text-red-400" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <ul class="list-inside list-disc space-y-1">
                                        <li v-for="(error, field) in formErrors" :key="field">
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                        <a
                            :href="route('admin.dashboard')"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing || !isFormValid"
                            class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <svg
                                v-if="form.processing"
                                class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <span v-if="form.processing">Creating User...</span>
                            <span v-else>Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
