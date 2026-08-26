

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
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

// Pipeline Kanban Lead — aktif hanya untuk yang boleh manage marketing
const board = document.querySelector('.kanban-board[data-editable]');
if (board) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    board.querySelectorAll('.kanban-list').forEach((list) => {
        new Sortable(list, {
            group: 'leads',
            animation: 150,
            ghostClass: 'opacity-40',
            onEnd: async (evt) => {
                const leadId = evt.item.dataset.leadId;
                const status = evt.to.dataset.status;

                try {
                    const res = await fetch(`/leads/${leadId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ status }),
                    });

                    if (!res.ok) throw new Error();

                    [evt.from, evt.to].forEach((l) => {
                        l.closest('.w-72').querySelector('[data-count]').textContent =
                            l.querySelectorAll('.kanban-card').length;
                    });
                } catch {
                    alert('Gagal mengubah status lead.');
                    window.location.reload();
                }
            },
        });
    });
}

