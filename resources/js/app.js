

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

