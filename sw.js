const CACHE_NAME = 'st-charles-lwanga-portal-v1';
const ASSETS_TO_CACHE = [
  '/St_Charles_Lwanga_Regiment_Portal/',
  '/St_Charles_Lwanga_Regiment_Portal/home',
  '/St_Charles_Lwanga_Regiment_Portal/index.php',
  '/St_Charles_Lwanga_Regiment_Portal/assets/css/style.css',
  '/St_Charles_Lwanga_Regiment_Portal/assets/js/main.js',
  '/St_Charles_Lwanga_Regiment_Portal/assets/images/logo/original_logo.jpeg',
  '/St_Charles_Lwanga_Regiment_Portal/assets/images/other/homepage.jpg',
  '/St_Charles_Lwanga_Regiment_Portal/assets/images/other/parish.jpg',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('PWA: Caching assets');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      // Return cached response if found, otherwise fetch from network
      return cachedResponse || fetch(event.request).catch(() => {
        // Fallback for offline (optional: return a custom offline page)
        if (event.request.mode === 'navigate') {
          return caches.match('/St_Charles_Lwanga_Regiment_Portal/');
        }
      });
    })
  );
});
