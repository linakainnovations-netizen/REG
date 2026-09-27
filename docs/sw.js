/* St. Charles Lwanga Regiment Portal — demo service worker (phones install + offline shell) */
const CACHE = "lwanga-demo-v1";
const CORE = [
  "./",
  "./index.html",
  "./login.html",
  "./dashboard.html",
  "./manifest.json",
  "./assets/css/style.css",
  "./assets/js/main.js",
  "./assets/icon-192.png",
  "./assets/icon-512.png",
  "./js/demo-data.js",
  "./js/snapshot-demo.js",
  "./js/app.js",
  "./dashboard/admin_priest/assets/main/dashboard-base.css",
  "./dashboard/admin_priest/assets/main/head-side.css",
  "./dashboard/admin_priest/assets/main/main.css"
];

self.addEventListener("install", (e) => {
  e.waitUntil(caches.open(CACHE).then((c) => c.addAll(CORE)).then(() => self.skipWaiting()));
});

self.addEventListener("activate", (e) => {
  e.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))).then(() => self.clients.claim())
  );
});

self.addEventListener("fetch", (e) => {
  if (e.request.method !== "GET") return;
  const url = new URL(e.request.url);
  if (url.origin !== self.location.origin) return; // leave CDN (charts, fonts) to network
  if (e.request.mode === "navigate") {
    // pages: fresh first, cache fallback (demo still opens offline)
    e.respondWith(fetch(e.request).catch(() => caches.match(e.request).then((r) => r || caches.match("./index.html"))));
    return;
  }
  e.respondWith(caches.match(e.request).then((hit) => hit || fetch(e.request).then((res) => {
    const copy = res.clone();
    caches.open(CACHE).then((c) => c.put(e.request, copy));
    return res;
  }).catch(() => hit)));
});
