importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.5.0/workbox-sw.js');

const workboxSW = {precache: (arr) => arr};

const fileManifest = workboxSW.precache([]);

if (workbox) {
    console.log(`Yay! Workbox is loaded 🎉`);

    console.log(workbox);

    workbox.precaching.precache(fileManifest);

} else {
    console.log(`Boo! Workbox didn't load 😬`);
}

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.open('my-cache').then
    );
});

// let deferredPrompt;
//
// window.addEventListener('beforeinstallprompt', (e) => {
//     // Prevent the mini-infobar from appearing on mobile
//     e.preventDefault();
//     // Stash the event so it can be triggered later.
//     deferredPrompt = e;
//     // Update UI notify the user they can install the PWA
//     console.log(e);
// });
//
// window.addEventListener('appinstalled', (evt) => {
//     console.log('a2hs installed');
// });

// Notification({
//     type: 'info',
//     position: 'bottom-left',
//     title: 'You can install me',
//     message: '',
// });
// importScripts('workbox-sw.prod.v2.1.3.js');

/**
 * DO NOT EDIT THE FILE MANIFEST ENTRY
 *
 * The method precache() does the following:
 * 1. Cache URLs in the manifest to a local cache.
 * 2. When a network request is made for any of these URLs the response
 *    will ALWAYS comes from the cache, NEVER the network.
 * 3. When the service worker changes ONLY assets with a revision change are
 *    updated, old cache entries are left as is.
 *
 * By changing the file manifest manually, your users may end up not receiving
 * new versions of files because the revision hasn't changed.
 *
 * Please use workbox-build or some other tool / approach to generate the file
 * manifest which accounts for changes to local files and update the revision
 * accordingly.
 */
// const fileManifest = [
//     {
//         "url": "js/manifest.js",
//         "revision": "41f053ba9a94d81b39f82277264c0669"
//     },
//     {
//         "url": "service-worker.js",
//         "revision": "bbfcb52f3e1aae1a21a7c25913f4ee60"
//     },
//     {
//         "url": "fonts/element-icons.ttf",
//         "revision": "732389ded34cb9c52dd88271f1345af9"
//     },
//     {
//         "url": "fonts/element-icons.woff",
//         "revision": "535877f50039c0cb49a6196a5b7517cd"
//     }
// ];
//
// const workboxSW = new self.WorkboxSW({
//     "skipWaiting": true,
//     "clientsClaim": true
// });
// workboxSW.precache(fileManifest);
// workboxSW.router.registerRoute(/http:\/\/diploma.loc/, workboxSW.strategies.networkFirst({
//     "cacheName": "Diploma-local"
// }), 'GET');
// workboxSW.router.registerRoute(/https:\/\/fonts.(googleapis|gstatic).com/, workboxSW.strategies.cacheFirst({
//     "cacheName": "google-fonts"
// }), 'GET');
