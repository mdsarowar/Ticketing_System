<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Support Tickets</h1>
                    <p class="text-gray-600 mt-1">Manage and track your support requests</p>
                </div>
                <Link
                    href="/tickets/create"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition inline-flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Ticket
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            v-model="filters.status"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">All Status</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select
                            v-model="filters.priority"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">All Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="filters.category"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">All Categories</option>
                            <option value="technical">Technical</option>
                            <option value="billing">Billing</option>
                            <option value="general">General</option>
                            <option value="complaint">Complaint</option>
                            <option value="feature_request">Feature Request</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input
                            v-model="filters.search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Search tickets..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>
                </div>
            </div>

            <!-- Tickets List -->
            <div v-if="filteredTickets.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <TicketCard
                    v-for="ticket in filteredTickets"
                    :key="ticket.id"
                    :ticket="ticket"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No tickets found</h3>
                <p class="text-gray-600 mb-6">
                    {{ hasFilters ? 'Try adjusting your filters' : 'Get started by creating your first ticket' }}
                </p>
                <Link
                    v-if="!hasFilters"
                    href="/tickets/create"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create First Ticket
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../layouts/AppLayout.vue'
import TicketCard from '../../Component/TicketCard.vue'

const props = defineProps({
    tickets: {
        type: Array,
        required: true,
    },
})

const filters = ref({
    status: '',
    priority: '',
    category: '',
    search: '',
})

const filteredTickets = computed(() => {
    let result = props.tickets

    if (filters.value.status) {
        result = result.filter(ticket => ticket.status === filters.value.status)
    }

    if (filters.value.priority) {
        result = result.filter(ticket => ticket.priority === filters.value.priority)
    }

    if (filters.value.category) {
        result = result.filter(ticket => ticket.category === filters.value.category)
    }

    if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        result = result.filter(ticket =>
            ticket.subject.toLowerCase().includes(search) ||
            ticket.description.toLowerCase().includes(search)
        )
    }

    return result
})

const hasFilters = computed(() => {
    return filters.value.status || filters.value.priority || filters.value.category || filters.value.search
})

const applyFilters = () => {
    // Filters are reactive, so this is just for future extensions
}
</script>
