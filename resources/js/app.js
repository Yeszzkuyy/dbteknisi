

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.ApexCharts = ApexCharts;

document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', (initialUnread = 0) => ({
        open: false,
        unread: initialUnread,
        toast: false,
        toastTimer: null,
        init() {
            this.refresh();
            this.timer = setInterval(() => this.refresh(), 30000);
        },
        async refresh() {
            try {
                const res = await fetch('/notifications/unread-count');
                const data = await res.json();
                if (data.count > this.unread) this.showToast();
                this.unread = data.count;
            } catch (e) {}
        },
        showToast() {
            this.toast = true;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => (this.toast = false), 10000);
        },
    }));

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

