

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.ApexCharts = ApexCharts;

document.addEventListener('alpine:init', () => {
    Alpine.store('notif', {
        unread: window.notifInit?.unread ?? 0,
        unassigned: window.notifInit?.unassigned ?? 0,
        items: window.notifInit?.items ?? [],
        toast: false,
        toastTimer: null,
        init() {
            this.refresh();
            this.timer = setInterval(() => this.refresh(), 5000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.refresh();
            });
            window.addEventListener('focus', () => this.refresh());
        },
        async refresh() {
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

