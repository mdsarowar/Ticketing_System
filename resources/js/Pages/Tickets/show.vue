<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <Link href="/tickets" class="text-indigo-600 hover:text-indigo-700 font-medium inline-flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Tickets
                </Link>
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ ticket.subject }}</h1>
                        <p class="text-gray-600 mt-1">Ticket #{{ ticket.id }}</p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-if="canEdit"
                            :href="`/tickets/${ticket.id}/edit`"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </Link>
                        <button
                            v-if="canDelete"
                            @click="deleteTicket"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (Left Column - 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Ticket Details -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center gap-3 mb-4 pb-4 border-b">
                            <div class="bg-indigo-100 p-2 rounded-full">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Created by</p>
                                <p class="font-semibold text-gray-800">{{ ticket.user.name }}</p>
                            </div>
                            <div class="ml-auto text-right">
                                <p class="text-sm text-gray-600">{{ formatDate(ticket.created_at) }}</p>
                            </div>
                        </div>

                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ ticket.description }}</p>
                        </div>

                        <div v-if="ticket.attachment" class="mt-4 pt-4 border-t">
                            <p class="text-sm font-medium text-gray-700 mb-2">Attachment: </p>
                            <img
                                :src="`/${ticket.attachment}`"
                                :alt="ticket.subject"
                                class="max-w-md rounded-lg border shadow-sm"
                            />
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Comments</h2>

                        <!-- Add Comment Form -->
                        <form @submit.prevent="submitComment" class="mb-6">
                            <textarea
                                v-model="commentForm.comment"
                                rows="3"
                                placeholder="Add a comment..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none mb-3"
                                required
                            ></textarea>
                            <button
                                type="submit"
                                :disabled="commentForm.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition disabled:opacity-50"
                            >
                                <span v-if="!commentForm.processing">Add Comment</span>
                                <span v-else>Adding...</span>
                            </button>
                        </form>

                        <!-- Comments List -->
                        <div v-if="ticket.comments && ticket.comments.length > 0" class="space-y-4">
                            <div
                                v-for="comment in ticket.comments"
                                :key="comment.id"
                                class="border-l-4 border-indigo-500 bg-gray-50 p-4 rounded-r-lg"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-indigo-100 p-1 rounded-full">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ comment.user.name }}</span>
                                        <span
                                            v-if="comment.user.role === 'admin'"
                                            class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full"
                                        >
                                            Admin
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
                                </div>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ comment.comment }}</p>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            No comments yet. Be the first to comment!
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right Column - 1/3) -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ticket Information</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Status</p>
                                <span
                                    :class="[
                                        'inline-block px-3 py-1 text-sm rounded-full font-medium',
                                        getStatusColor(ticket.status)
                                    ]"
                                >
                                    {{ formatStatus(ticket.status) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Priority</p>
                                <span
                                    :class="[
                                        'inline-block px-3 py-1 text-sm rounded-full font-medium',
                                        getPriorityColor(ticket.priority)
                                    ]"
                                >
                                    {{ ticket.priority.toUpperCase() }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Category</p>
                                <span class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full font-medium">
                                    {{ formatCategory(ticket.category) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Created</p>
                                <p class="text-gray-800 font-medium">{{ formatDateFull(ticket.created_at) }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Last Updated</p>
                                <p class="text-gray-800 font-medium">{{ formatDateFull(ticket.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Live Chat Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Live Chat
                        </h3>
                        <ChatBox
                            :ticket-id="ticket.id"
                            :initial-messages="ticket.chat_messages || []"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '../../layouts/AppLayout.vue'
import ChatBox from '../../Component/ChatBox.vue'

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const commentForm = useForm({
    comment: '',
})

const canEdit = computed(() => {
    return page.props.auth.user.role === 'admin' ||
        props.ticket.user_id === page.props.auth.user.id
})

const canDelete = computed(() => {
    return page.props.auth.user.role === 'admin' ||
        props.ticket.user_id === page.props.auth.user.id
})

const submitComment = () => {
    commentForm.post(`/tickets/${props.ticket.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset()
        },
    })
}

const deleteTicket = () => {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        router.delete(`/tickets/${props.ticket.id}`)
    }
}

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

const formatDateFull = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>
