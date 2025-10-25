import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel.
 */
//
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
//
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
    },
});

// console.log('✅ Laravel Echo initialized');
// console.log('Echo object:', window.Echo);
// console.log('Reverb config:', {
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     host: import.meta.env.VITE_REVERB_HOST,
//     port: import.meta.env.VITE_REVERB_PORT,
//     scheme: import.meta.env.VITE_REVERB_SCHEME,
// });
