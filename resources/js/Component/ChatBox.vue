<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-[600px]">
        <!-- Chat Header -->
        <div class="bg-indigo-600 text-white px-6 py-4">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Live Chat
                <span v-if="isConnected" class="ml-auto flex items-center gap-1 text-xs text-green-300">
          <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
          Connected
        </span>
            </h3>
        </div>

        <!-- Messages Container -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
            <div
                v-for="message in messages"
                :key="message.id"
                :class="[
          'flex',
          message.user_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'
        ]"
            >
                <div
                    :class="[
            'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
            message.user_id === $page.props.auth.user.id
              ? 'bg-indigo-600 text-white rounded-br-none'
              : 'bg-white text-gray-800 rounded-bl-none shadow'
          ]"
                >
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold">{{ message.user.name }}</span>
                        <span
                            v-if="message.user.role === 'admin'"
                            :class="[
                'text-xs px-2 py-0.5 rounded-full',
                message.user_id === $page.props.auth.user.id
                  ? 'bg-indigo-500 text-white'
                  : 'bg-purple-100 text-purple-800'
              ]"
                        >
              Admin
            </span>
                    </div>
                    <p class="whitespace-pre-wrap break-words">{{ message.message }}</p>
                    <p
                        :class="[
              'text-xs mt-1',
              message.user_id === $page.props.auth.user.id
                ? 'text-indigo-200'
                : 'text-gray-500'
            ]"
                    >
                        {{ formatTime(message.created_at) }}
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="messages.length === 0" class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-500">No messages yet. Start the conversation!</p>
            </div>
        </div>

        <!-- Message Input -->
        <form @submit.prevent="sendMessage" class="border-t border-gray-200 p-4 bg-white">
            <div class="flex gap-2">
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    :disabled="sending"
                />
                <button
                    type="submit"
                    :disabled="!newMessage.trim() || sending"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span v-if="!sending">Send</span>
                    <span v-else>...</span>
                </button>
            </div>
            <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    ticketId: {
        type: Number,
        required: true,
    },
    initialMessages: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const messages = ref([...props.initialMessages])
const newMessage = ref('')
const sending = ref(false)
const messagesContainer = ref(null)
const isConnected = ref(false)
const error = ref('')

// Scroll to bottom
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

// Send message
const sendMessage = () => {
    if (!newMessage.value.trim() || sending.value) return

    const messageText = newMessage.value
    sending.value = true
    error.value = ''

    // Optimistic update
    const tempMessage = {
        id: Date.now(),
        ticket_id: props.ticketId,
        user_id: page.props.auth.user.id,
        message: messageText,
        created_at: new Date().toISOString(),
        user: {
            id: page.props.auth.user.id,
            name: page.props.auth.user.name,
            role: page.props.auth.user.role,
        },
    }

    messages.value.push(tempMessage)
    newMessage.value = ''
    scrollToBottom()

    router.post(
        `/tickets/${props.ticketId}/chat`,
        { message: messageText },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Message added successfully
            },
            onError: (errors) => {
                error.value = 'Failed to send message. Please try again.'
                // Remove optimistic message on error
                messages.value = messages.value.filter(m => m.id !== tempMessage.id)
            },
            onFinish: () => {
                sending.value = false
            },
        }
    )
}

// Format time
const formatTime = (timestamp) => {
    const date = new Date(timestamp)
    const now = new Date()
    const diff = now - date

    if (diff < 60000) return 'Just now'
    if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`
    if (diff < 86400000) return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// Setup Laravel Echo
onMounted(() => {
    scrollToBottom()

    if (window.Echo) {
        // Subscribe to private channel
        window.Echo.private(`ticket.${props.ticketId}`)
            .listen('.message.sent', (event) => {
                console.log('New message received:', event)

                // Add message if it's not from current user
                if (event.user_id !== page.props.auth.user.id) {
                    messages.value.push({
                        id: event.id,
                        ticket_id: event.ticket_id,
                        user_id: event.user_id,
                        message: event.message,
                        is_read: event.is_read,
                        created_at: event.created_at,
                        user: event.user,
                    })

                    scrollToBottom()
                }
            })
            .error((error) => {
                console.error('Echo connection error:', error)
                isConnected.value = false
            })

        isConnected.value = true
    } else {
        console.warn('Laravel Echo is not initialized')
    }
})

// Cleanup
onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave(`ticket.${props.ticketId}`)
    }
})
</script>
