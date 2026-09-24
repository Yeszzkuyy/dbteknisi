

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import ApexCharts from 'apexcharts';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.ApexCharts = ApexCharts;
window.marked = marked;
window.DOMPurify = DOMPurify;

/* ============================================================
   Appearance — mode (light/dark/system) × accent (theme)
   Token CSS di resources/css/themes/accent/*.css
   ============================================================ */
function applyAppearance(mode, accent) {
    const root = document.documentElement;
    const dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    root.classList.toggle('dark', dark);
    root.setAttribute('data-mode', mode);
    root.setAttribute('data-theme', accent);
    try {
        localStorage.setItem('appearance-mode', mode);
        localStorage.setItem('appearance-accent', accent);
    } catch (e) {}
    window.dispatchEvent(new CustomEvent('appearance:change'));
}

function persistAppearance(mode, accent) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch('/settings/appearance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: JSON.stringify({ theme: mode, accent }),
    }).catch(() => {});
}

window.getAppearanceColors = function () {
    const css = getComputedStyle(document.documentElement);
    const t = (name) => css.getPropertyValue(name).trim();
    const rgb = (name) => `rgb(${t(name).replace(/\s+/g, ', ')})`;
    return {
        dark: document.documentElement.classList.contains('dark'),
        theme: document.documentElement.dataset.theme || 'ocean',
        accent500: rgb('--accent-500'),
        accent600: rgb('--accent-600'),
        accent700: rgb('--accent-700'),
        info: rgb('--semantic-info'),
        success: rgb('--semantic-success'),
        warning: rgb('--semantic-warning'),
        danger: rgb('--semantic-danger'),
        cardBg: t('--card-bg'),
    };
};

/* ============================================================
   Web Push (VAPID) — daftar SW diam-diam, subscribe hanya
   saat user menekan "Aktifkan" (Settings > Notifikasi).
   Dipakai lewat window.WebPush: status(), enable(), disable().
   ============================================================ */
function urlBase64ToUint8Array(base64) {
    const padding = '='.repeat((4 - (base64.length % 4)) % 4);
    const raw = window.atob((base64 + padding).replace(/-/g, '+').replace(/_/g, '/'));
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
}

async function swRegistration() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return null;
    try {
        return await navigator.serviceWorker.register('/sw.js');
    } catch (e) {
        return null;
    }
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

window.WebPush = {
    supported: 'serviceWorker' in navigator && 'PushManager' in window,

    async status() {
        if (!this.supported || !window.vapidPublicKey) return 'unsupported';
        if (Notification.permission === 'denied') return 'blocked';
        const reg = await swRegistration();
        if (!reg) return 'unsupported';
        const sub = await reg.pushManager.getSubscription();
        return sub ? 'subscribed' : Notification.permission;
    },

    async enable() {
        if (!this.supported || !window.vapidPublicKey) return 'unsupported';
        if (Notification.permission === 'denied') return 'blocked';
        // Minta izin eksplisit dulu (wajib user gesture; sebagian browser
        // mengabaikan prompt implisit dari subscribe()).
        if (Notification.permission === 'default') {
            const perm = await Notification.requestPermission();
            if (perm !== 'granted') return perm;
        }
        const reg = await swRegistration();
        if (!reg) return 'unsupported';
        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(window.vapidPublicKey),
            });
        }
        const json = sub.toJSON();
        await fetch('/push-subscriptions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
            body: JSON.stringify({
                endpoint: sub.endpoint,
                keys: json.keys,
                contentEncoding: (PushManager.supportedContentEncodings || ['aes128gcm'])[0],
            }),
        });
        return 'subscribed';
    },

    async disable() {
        const reg = await swRegistration();
        const sub = reg ? await reg.pushManager.getSubscription() : null;
        if (sub) {
            try {
                await fetch('/push-subscriptions', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
                    body: JSON.stringify({ endpoint: sub.endpoint }),
                });
            } catch (e) {}
            await sub.unsubscribe();
        }
        return 'unsubscribed';
    },

    init() {
        // Daftarkan SW lebih awal agar push bisa tiba walau tab ditutup.
        if (this.supported && window.vapidPublicKey) swRegistration();
    },
};

window.WebPush.init();

document.addEventListener('alpine:init', () => {
    Alpine.store('appearance', {
        mode: window.__appearanceMode || 'system',
        accent: window.__appearanceAccent || 'ocean',
        media: window.matchMedia('(prefers-color-scheme: dark)'),
        init() {
            // Saat mode=system, ikuti perubahan tema OS tanpa reload.
            this.media.addEventListener('change', () => {
                if (this.mode === 'system') applyAppearance(this.mode, this.accent);
            });
        },
        setMode(mode) {
            this.mode = mode;
            applyAppearance(this.mode, this.accent);
            persistAppearance(this.mode, this.accent);
        },
        setAccent(accent) {
            this.accent = accent;
            applyAppearance(this.mode, this.accent);
            persistAppearance(this.mode, this.accent);
        },
    });
    Alpine.store('appearance').init();
    Alpine.store('notif', {
        unread: window.notifInit?.unread ?? 0,
        unassigned: window.notifInit?.unassigned ?? 0,
        items: window.notifInit?.items ?? [],
        toast: false,
        toastTimer: null,
        init() {
            if (window.notifInit === undefined) return;
            this.refresh();
            this.timer = setInterval(() => this.refresh(), 5000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.refresh();
            });
            window.addEventListener('focus', () => this.refresh());
        },
        async refresh() {
            if (window.notifInit === undefined) return;
            try {
                const res = await fetch('/notifications/status');
                const data = await res.json();
                if (data.unread > this.unread) this.showToast();
                this.unread = data.unread;
                this.unassigned = data.unassigned;
                this.items = data.items ?? [];
            } catch (e) {}
        },
        async markRead(id) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const item = this.items.find((n) => n.id === id);
            if (item) item.read = true;
            try {
                const res = await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    keepalive: true,
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (typeof data.unread === 'number') this.unread = data.unread;
            } catch (e) {}
        },
        async remove(id) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const item = this.items.find((n) => n.id === id);
            this.items = this.items.filter((n) => n.id !== id);
            if (item && !item.read) this.unread = Math.max(0, this.unread - 1);
            try {
                const res = await fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (typeof data.unread === 'number') this.unread = data.unread;
            } catch (e) {}
        },
        showToast() {
            this.toast = true;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => (this.toast = false), 8000);
        },
    });
    Alpine.store('notif').init();

    Alpine.data('counter', (target, duration = 900) => ({
        display: 0,
        start() {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce || target <= 0) { this.display = target; return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 4);
                this.display = Math.round(target * eased);
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        },
    }));
});

Alpine.start();

/* ============================================================
   Branched sidebar paket B — garis reach aktif "menggambar"
   tiap halaman dimuat (ala React Bits drawDuration).
   Server me-render garis dalam keadaan jadi; di sini sembunyikan
   dulu sebelum paint pertama, lalu kembalikan ke 0 sehingga
   transisi stroke-dashoffset di CSS yang menganimasikannya.
   Tanpa JS / reduced-motion: garis langsung tampil (fallback aman).
   ============================================================ */
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    requestAnimationFrame(function () {
        var active = [];
        document.querySelectorAll('.sidebar .branched-reach').forEach(function (p) {
            if (p.style.strokeDashoffset !== '0' && p.style.strokeDashoffset !== '0px') return;
            if (!p.style.strokeDasharray) return;
            p.style.strokeDashoffset = p.style.strokeDasharray;
            active.push(p);
        });
        if (!active.length) return;
        requestAnimationFrame(function () {
            active.forEach(function (p) {
                p.style.strokeDashoffset = '0';
            });
        });
    });
})();

