/**
 * Laravel Echo Configuration for Reverb WebSocket
 * 
 * This file initializes Laravel Echo to connect to the Reverb WebSocket server
 * for real-time updates from devices.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Echo
window.Pusher = Pusher;

// Initialize Echo with Reverb configuration
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.AdmixConfig?.reverb?.key || import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.AdmixConfig?.reverb?.host || import.meta.env.VITE_REVERB_HOST,
    wsPort: window.AdmixConfig?.reverb?.port || import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: window.AdmixConfig?.reverb?.port || import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: (window.AdmixConfig?.reverb?.scheme || import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    // Tuning for faster reconnection
    unavailable_timeout: 1000, 
    pong_timeout: 1000,
    activityTimeout: 1000,
});

// Log connection status (for debugging)
// Connection Status Handling
// Connection Status Handling
// Log connection status (for debugging)
// Connection Status Handling
const updateStatus = (status, color, message) => {
    // Legacy support (invisible div)
    const indicator = document.getElementById('websocket-status');
    if (indicator) {
        // Just store for legacy debug if needed
        indicator.dataset.status = status;
    }
    
    // New Alpine hook
    if (window.updateSystemStatus) {
        window.updateSystemStatus(status);
    }
    
    if (import.meta.env.DEV) {
        // console.log(`[WebSocket] ${status}: ${message}`);
    }
};

window.Echo.connector.pusher.connection.bind('connected', () => {
    updateStatus('connected', 'bg-green-500', 'Connected');
});

window.Echo.connector.pusher.connection.bind('unavailable', () => {
    updateStatus('disconnected', 'bg-red-500', 'Disconnected');
});

window.Echo.connector.pusher.connection.bind('failed', () => {
    updateStatus('disconnected', 'bg-red-600', 'Connection Failed');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    updateStatus('disconnected', 'bg-gray-400', 'Disconnected');
});

window.Echo.connector.pusher.connection.bind('connecting', () => {
    updateStatus('connecting', 'bg-yellow-400', 'Connecting...');
});

export default window.Echo;
