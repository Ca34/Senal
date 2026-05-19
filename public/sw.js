const CACHE_NAME = 'senal-cache-v1';
const urlsToCache = [
  '/rutas',
  '/manifest.json',
  // Se pueden añadir más assets locales
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  // Solo interceptar peticiones GET
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request);
      })
  );
});
