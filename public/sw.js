const CACHE_NAME = 'admix-central-v9';
const urlsToCache = [
    '/',
    '/login',
    '/images/logo.png',
    // Add other assets here as they are identified
];

self.addEventListener('install', event => {
    // Force the waiting service worker to become the active service worker.
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        Promise.all([
            // Delete old caches
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheWhitelist.indexOf(cacheName) === -1) {
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            // Take control of all clients immediately
            self.clients.claim()
        ])
    );
});

self.addEventListener('fetch', event => {
    // Check if the request is for an HTML page (navigation)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request, { ignoreSearch: true });
                })
        );
    } else {
        // For assets (images, css, js), continue using Cache-First
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true })
                .then(response => {
                    if (response) {
                        return response;
                    }
                    return fetch(event.request);
                })
        );
    }
});


