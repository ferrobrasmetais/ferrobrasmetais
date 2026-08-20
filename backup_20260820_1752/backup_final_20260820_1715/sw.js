// ============================================================
// SERVICE WORKER - FERROBRAS METAIS
// ============================================================

console.log('✅ Service Worker carregado!');

const CACHE_NAME = 'ferrobras-v1';

// Instalação
self.addEventListener('install', function(event) {
    console.log('📦 Instalando Service Worker...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            console.log('✅ Cache aberto');
            return cache.addAll([
                '/',
                '/index.php'
            ]);
        }).then(function() {
            console.log('✅ Service Worker instalado!');
            return self.skipWaiting();
        })
    );
});

// Ativação
self.addEventListener('activate', function(event) {
    console.log('⚡ Ativando Service Worker...');
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('🗑️ Removendo cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(function() {
            console.log('✅ Service Worker ativado!');
            return self.clients.claim();
        })
    );
});

// Interceptação
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
});

// Notificações
self.addEventListener('push', function(event) {
    console.log('📨 Notificação recebida!');
    const options = {
        body: event.data ? event.data.text() : '📩 Nova mensagem da Ferrobras Metais!',
        icon: '/assets/images/logo/ferrobrasmetais_logo.webp',
        vibrate: [200, 100, 200],
        data: { dateOfArrival: Date.now() },
        actions: [
            { action: 'abrir', title: 'Abrir Site' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('🛒 Ferrobras Metais', options)
    );
});

// Clique na notificação
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.action === 'abrir') {
        event.waitUntil(clients.openWindow('https://ferrobrasmetais.com.br/'));
    }
});
