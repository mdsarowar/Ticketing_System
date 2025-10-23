<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <Link href="/dashboard" class="text-xl font-bold text-indigo-600">
                            Support Tickets
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-4">
                        <Link
                            href="/dashboard"
                            :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition',
                $page.component === 'Dashboard'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/tickets"
                            :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition',
                $page.component.startsWith('Tickets')
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
                        >
                            Tickets
                        </Link>

                        <!-- User Info -->
                        <div class="flex items-center space-x-3 ml-4 pl-4 border-l">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <span
                                    :class="[
                    'text-xs px-2 py-1 rounded-full',
                    $page.props.auth.user.role === 'admin'
                      ? 'bg-purple-100 text-purple-800'
                      : 'bg-blue-100 text-blue-800'
                  ]"
                                >
                  {{ $page.props.auth.user.role }}
                </span>
                            </div>
                            <button
                                @click="logout"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition"
                            >
                                Logout
                            </button>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-gray-700 hover:text-indigo-600"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    v-if="!mobileMenuOpen"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    v-else
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link
                        href="/dashboard"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Dashboard
                    </Link>
                    <Link
                        href="/tickets"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Tickets
                    </Link>
                    <button
                        @click="logout"
                        class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ $page.props.flash.success }}
            </div>
        </div>
        <div v-if="$page.props.flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const mobileMenuOpen = ref(false)

const logout = () => {
    router.post('/logout')
}
</script>
