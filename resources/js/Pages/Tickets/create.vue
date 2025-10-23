<template>
    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <Link href="/tickets" class="text-indigo-600 hover:text-indigo-700 font-medium inline-flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Tickets
                </Link>
                <h1 class="text-3xl font-bold text-gray-800">Create New Ticket</h1>
                <p class="text-gray-600 mt-1">Submit a support request</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form @submit.prevent="submit">
                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Brief description of your issue"
                        />
                        <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">
                            {{ form.errors.subject }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            required
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                            placeholder="Please provide detailed information about your issue..."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Category & Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="category"
                                v-model="form.category"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="">Select Category</option>
                                <option value="technical">Technical Issue</option>
                                <option value="billing">Billing</option>
                                <option value="general">General Inquiry</option>
                                <option value="complaint">Complaint</option>
                                <option value="feature_request">Feature Request</option>
                            </select>
                            <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                                {{ form.errors.category }}
                            </p>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <p v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                                {{ form.errors.priority }}
                            </p>
                        </div>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-6">
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachment (Optional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition">
                            <input
                                id="attachment"
                                type="file"
                                @change="handleFileChange"
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                class="hidden"
                            />
                            <label for="attachment" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-sm text-gray-600">
                                    <span class="text-indigo-600 font-medium">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    JPG, PNG, PDF, DOC up to 5MB
                                </p>
                            </label>
                        </div>
                        <p v-if="selectedFileName" class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ selectedFileName }}
                        </p>
                        <p v-if="form.errors.attachment" class="text-red-500 text-sm mt-1">
                            {{ form.errors.attachment }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="!form.processing">Create Ticket</span>
                            <span v-else>Creating...</span>
                        </button>
                        <Link
                            href="/tickets"
                            class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '../../layouts/AppLayout.vue'

const form = useForm({
    subject: '',
    description: '',
    category: '',
    priority: '',
    attachment: null,
})

const selectedFileName = ref('')

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.attachment = file
        selectedFileName.value = file.name
    }
}

const submit = () => {
    form.post('/tickets', {
        onSuccess: () => {
            form.reset()
            selectedFileName.value = ''
        },
    })
}
</script>
