const CACHE_NAME = `diametrick-v1`;

self.addEventListener('install', event => {
    event.waitUntil((async () => {
        const cache = await caches.open(CACHE_NAME);
        cache.addAll([
            './jscripts/bundle.js',
            './styles/styles.min.css',
            './styles/fonts/Rubik.woff2',
        ]);
    })());
});