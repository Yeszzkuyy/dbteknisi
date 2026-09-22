/* Service Worker — Web Push (VAPID). Didaftarkan dari resources/js/app.js. */

self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'Notifikasi baru';
    const options = {
        body: data.body || '',
        icon: data.icon || '/favicon.png',
        badge: data.badge || '/favicon.png',
        data: { url: (data.data && data.data.url) || '/' },
        actions: data.actions || [{ title: 'Lihat', action: 'open' }],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
            for (const c of list) {
                if (c.url === url && 'focus' in c) return c.focus();
            }
            return clients.openWindow(url);
        })
    );
});
