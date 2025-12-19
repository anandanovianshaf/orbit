/* Orbit Service Worker (no external libs)
 * Caching strategy:
 * - precache: app shell pages + offline page + built assets (loaded dynamically from /build/manifest.json)
 * - runtime:
 *   - pages: network-first (fallback cache, then offline)
 *   - static assets: cache-first
 */

const VERSION = 'orbit-sw-v1';

const STATIC_CACHE = `${VERSION}-static`;
const PAGES_CACHE = `${VERSION}-pages`;

const OFFLINE_URL = '/offline';
const PRECACHE_URLS = [
  '/',
  OFFLINE_URL,
  '/manifest.webmanifest',
  '/favicon.ico',
  '/pwa/icon-192.png',
  '/pwa/icon-512.png',
];

async function precacheBuildAssets() {
  try {
    const res = await fetch('/build/manifest.json', { cache: 'no-store' });
    if (!res.ok) return [];
    const manifest = await res.json();

    const urls = new Set();
    for (const key of Object.keys(manifest)) {
      const item = manifest[key];
      if (item && item.file) urls.add(`/build/${item.file}`);
      if (item && Array.isArray(item.css)) item.css.forEach((f) => urls.add(`/build/${f}`));
      if (item && Array.isArray(item.imports)) item.imports.forEach((imp) => {
        if (manifest[imp]?.file) urls.add(`/build/${manifest[imp].file}`);
        if (Array.isArray(manifest[imp]?.css)) manifest[imp].css.forEach((f) => urls.add(`/build/${f}`));
      });
    }

    return Array.from(urls);
  } catch {
    return [];
  }
}

self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
    const cache = await caches.open(STATIC_CACHE);
    const buildAssets = await precacheBuildAssets();

    await cache.addAll([...PRECACHE_URLS, ...buildAssets]);
    self.skipWaiting();
  })());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys.map((k) => {
      if (!k.startsWith(VERSION)) return caches.delete(k);
      return Promise.resolve();
    }));

    self.clients.claim();
  })());
});

function isNavigationRequest(request) {
  return request.mode === 'navigate' || (request.method === 'GET' && request.headers.get('accept')?.includes('text/html'));
}

function isStaticAsset(url) {
  return url.pathname.startsWith('/build/')
    || url.pathname.startsWith('/storage/')
    || url.pathname.endsWith('.css')
    || url.pathname.endsWith('.js')
    || url.pathname.endsWith('.png')
    || url.pathname.endsWith('.jpg')
    || url.pathname.endsWith('.jpeg')
    || url.pathname.endsWith('.svg')
    || url.pathname.endsWith('.webp')
    || url.pathname.endsWith('.ico')
    || url.pathname.endsWith('.woff2');
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  // Navigation: network-first, fallback cache, then offline page
  if (isNavigationRequest(request)) {
    event.respondWith((async () => {
      try {
        const fresh = await fetch(request);
        const cache = await caches.open(PAGES_CACHE);
        cache.put(request, fresh.clone());
        return fresh;
      } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        const offline = await caches.match(OFFLINE_URL);
        return offline || new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
      }
    })());
    return;
  }

  // Static assets: cache-first
  if (isStaticAsset(url)) {
    event.respondWith((async () => {
      const cached = await caches.match(request);
      if (cached) return cached;
      const res = await fetch(request);
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, res.clone());
      return res;
    })());
    return;
  }

  // API: network-first with cache fallback
  if (url.pathname.startsWith('/api/')) {
    event.respondWith((async () => {
      try {
        const fresh = await fetch(request);
        const cache = await caches.open(`${VERSION}-api`);
        cache.put(request, fresh.clone());
        return fresh;
      } catch {
        const cached = await caches.match(request);
        return cached || new Response(JSON.stringify({ error: 'offline' }), {
          status: 503,
          headers: { 'Content-Type': 'application/json' },
        });
      }
    })());
  }
});
