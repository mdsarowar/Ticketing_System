<template>
    <Link
        :href="`/tickets/${ticket.id}`"
        class="block bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1"
    >
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex-1 pr-4">
                {{ ticket.subject }}
            </h3>
            <span
                :class="[
          'px-3 py-1 text-xs rounded-full font-medium whitespace-nowrap',
          getStatusColor(ticket.status)
        ]"
            >
        {{ formatStatus(ticket.status) }}
      </span>
        </div>

        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ ticket.description }}
        </p>

        <div class="flex flex-wrap items-center gap-2 mb-3">
      <span
          :class="[
          'px-2 py-1 rounded text-xs font-medium',
          getPriorityColor(ticket.priority)
        ]"
      >
        {{ ticket.priority.toUpperCase() }}
      </span>
            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
        {{ formatCategory(ticket.category) }}
      </span>
            <span v-if="ticket.attachment" class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium flex items-center gap-1">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
        </svg>
        Attachment
      </span>
        </div>

        <div class="flex items-center justify-between text-sm pt-3 border-t border-gray-100">
      <span class="text-gray-500 text-xs">
        {{ formatDate(ticket.created_at) }}
      </span>

            <div v-if="ticket.user && showCustomerName" class="text-xs text-gray-600">
                <span class="text-gray-400">By:</span>
                <span class="font-medium ml-1">{{ ticket.user.name }}</span>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const showCustomerName = computed(() => {
    return page.props.auth.user.role === 'admin'
})

const getStatusColor = (status) => {
    const colors = {
        open: 'bg-green-100 text-green-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        resolved: 'bg-blue-100 text-blue-800',
        closed: 'bg-gray-100 text-gray-800',
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityColor = (priority) => {
    const colors = {
        low: 'bg-gray-100 text-gray-700',
        medium: 'bg-blue-100 text-blue-700',
        high: 'bg-orange-100 text-orange-700',
        urgent: 'bg-red-100 text-red-700',
    }
    return colors[priority] || 'bg-gray-100 text-gray-700'
}

const formatStatus = (status) => {
    return status.replace('_', ' ').toUpperCase()
}

const formatCategory = (category) => {
    return category.replace('_', ' ').split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ')
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
