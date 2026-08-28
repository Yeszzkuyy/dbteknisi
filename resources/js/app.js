

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
    const pendingChanges = new Map(); // lead_id -> { newStatus, oldStatus, element }

    board.querySelectorAll('.kanban-list').forEach((list) => {
        new Sortable(list, {
            group: 'leads',
            animation: 150,
            ghostClass: 'opacity-40',
            onEnd: (evt) => {
                const leadId = evt.item.dataset.leadId;
                const newStatus = evt.to.dataset.status;
                const oldStatus = evt.from.dataset.status;

                // Track change locally
                if (oldStatus !== newStatus) {
                    pendingChanges.set(leadId, { newStatus, oldStatus, element: evt.item });
                } else {
                    pendingChanges.delete(leadId);
                }

                // Update UI counters
                [evt.from, evt.to].forEach((l) => {
                    l.closest('.w-72').querySelector('[data-count]').textContent =
                        l.querySelectorAll('.kanban-card').length;
                });

                // Show/hide save button
                updateSaveButton();
            },
        });
    });

    function updateSaveButton() {
        let saveBtn = document.getElementById('pipeline-save-btn');
        const count = pendingChanges.size;

        if (count === 0) {
            if (saveBtn) saveBtn.remove();
            return;
        }

        if (!saveBtn) {
            saveBtn = document.createElement('button');
            saveBtn.id = 'pipeline-save-btn';
            saveBtn.className = 'px-5 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition fixed bottom-6 right-6 shadow-lg z-50 flex items-center gap-2';
            saveBtn.onclick = savePendingChanges;
            document.body.appendChild(saveBtn);
        }

        saveBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Simpan Perubahan (${count})`;
    }

    async function savePendingChanges() {
        if (pendingChanges.size === 0) return;

        const saveBtn = document.getElementById('pipeline-save-btn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = `<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`;
        }

        const changes = Array.from(pendingChanges.entries()).map(([leadId, data]) => ({
            lead_id: parseInt(leadId),
            status: data.newStatus,
        }));

        try {
            const res = await fetch('/leads/batch-status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ changes }),
            });

            if (!res.ok) throw new Error();

            const data = await res.json();
            pendingChanges.clear();

            // Remove save button
            if (saveBtn) saveBtn.remove();

            // Show success toast
            showToast(`Berhasil menyimpan ${data.updated} perubahan`);
        } catch {
            alert('Gagal menyimpan perubahan. Silakan coba lagi.');
            if (saveBtn) {
                saveBtn.disabled = false;
                updateSaveButton();
            }
        }
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-20 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg z-50 transition-opacity flex items-center gap-2';
        toast.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

