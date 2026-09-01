<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Pipeline Lead</h1>
            <p class="text-slate-500 mt-1">Geser kartu antar kolom untuk mengubah status lead</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('leads.index') }}"
               class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition">
                Tabel Lead
            </a>
            @can('manage-marketing')
                <a href="{{ route('leads.create') }}"
                   class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    + Tambah Lead
                </a>
            @endcan
        </div>
    </div>

    <div class="kanban-board overflow-x-auto pb-4"
         @if(auth()->user()->can('manage-marketing')) data-editable="1" @endif>
        <div class="flex gap-4 min-w-max items-start">
            @foreach($statuses as $status)
                @php
                    $columnLeads = $leads->where('status', $status);
                    $colors = [
                        'new' => 'bg-blue-100 text-blue-800',
                        'contacted' => 'bg-yellow-100 text-yellow-800',
                        'qualified' => 'bg-purple-100 text-purple-800',
                        'proposal' => 'bg-orange-100 text-orange-800',
                        'won' => 'bg-green-100 text-green-800',
                        'lost' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <div class="w-72 shrink-0 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-600">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-600">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$status] ?? 'bg-slate-100 text-slate-800' }}">
                            {{ ucfirst($status) }}
                        </span>
                        <span class="text-sm font-semibold text-slate-500" data-count>{{ $columnLeads->count() }}</span>
                    </div>
                    <div class="kanban-list p-3 space-y-3 min-h-24" data-status="{{ $status }}">
                        @foreach($columnLeads as $lead)
                            <div class="kanban-card bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-3 shadow-sm hover:shadow transition"
                                 data-lead-id="{{ $lead->id }}">
                                <div class="flex items-center justify-between mb-1">
                                    @if($lead->pt_group)
                                        <span class="inline-flex px-1.5 py-0.5 rounded {{ \App\Models\Lead::PT_COLORS[$lead->pt_group] ?? 'bg-indigo-50 text-indigo-700' }} text-[11px] font-semibold">
                                            {{ $lead->pt_group }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] text-slate-400">{{ $lead->incoming_date?->format('d M y') }}</span>
                                </div>
                                <a href="{{ route('leads.show', $lead) }}"
                                   class="block font-semibold text-slate-800 hover:text-blue-600 leading-snug">
                                    {{ $lead->customer->name ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                    {{ $lead->kebutuhan ? Str::limit($lead->kebutuhan, 60) : '-' }}
                                </p>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 dark:border-slate-600">
                                    <span class="text-[11px] text-slate-500">
                                        {{ $lead->segment ? \App\Http\Controllers\LeadController::label($lead->segment) : '' }}
                                    </span>
                                    <span class="text-[11px] font-medium text-slate-600">
                                        {{ $lead->assignee?->name }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @can('manage-marketing')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var board = document.querySelector('.kanban-board[data-editable]');
        if (!board) return;
        if (typeof Sortable === 'undefined') {
            console.warn('SortableJS tidak tersedia, drag & drop pipeline dinonaktifkan.');
            return;
        }

        var csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        var pendingChanges = new Map();

        board.querySelectorAll('.kanban-list').forEach(function (list) {
            new Sortable(list, {
                group: 'leads',
                animation: 150,
                ghostClass: 'opacity-40',
                onEnd: function (evt) {
                    var leadId = evt.item.dataset.leadId;
                    var newStatus = evt.to.dataset.status;
                    var oldStatus = evt.from.dataset.status;

                    if (oldStatus !== newStatus) {
                        pendingChanges.set(leadId, { newStatus: newStatus, oldStatus: oldStatus, element: evt.item });
                    } else {
                        pendingChanges.delete(leadId);
                    }

                    [evt.from, evt.to].forEach(function (l) {
                        var col = l.closest('.w-72');
                        if (col) {
                            var counter = col.querySelector('[data-count]');
                            if (counter) counter.textContent = l.querySelectorAll('.kanban-card').length;
                        }
                    });

                    updateSaveButton();
                },
            });
        });

        function updateSaveButton() {
            var saveBtn = document.getElementById('pipeline-save-btn');
            var count = pendingChanges.size;

            if (count === 0) {
                if (saveBtn) saveBtn.remove();
                return;
            }

            if (!saveBtn) {
                saveBtn = document.createElement('button');
                saveBtn.id = 'pipeline-save-btn';
                saveBtn.className = 'px-5 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold transition fixed bottom-6 right-6 shadow-lg z-50 flex items-center gap-2';
                saveBtn.onclick = savePendingChanges;
                document.body.appendChild(saveBtn);
            }

            saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Simpan Perubahan (' + count + ')';
        }

        function savePendingChanges() {
            if (pendingChanges.size === 0) return;

            var saveBtn = document.getElementById('pipeline-save-btn');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
            }

            var changes = Array.from(pendingChanges.entries()).map(function (entry) {
                return { lead_id: parseInt(entry[0]), status: entry[1].newStatus };
            });

            fetch('{{ route('leads.batch-status') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ changes: changes }),
            }).then(function (res) {
                if (!res.ok) throw new Error();
                return res.json();
            }).then(function (data) {
                pendingChanges.clear();
                if (saveBtn) saveBtn.remove();
                showToast('Berhasil menyimpan ' + data.updated + ' perubahan');
            }).catch(function () {
                alert('Gagal menyimpan perubahan. Silakan coba lagi.');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    updateSaveButton();
                }
            });
        }

        function showToast(message) {
            var toast = document.createElement('div');
            toast.className = 'fixed bottom-20 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg z-50 transition-opacity flex items-center gap-2';
            toast.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> ' + message;
            document.body.appendChild(toast);
            setTimeout(function () {
                toast.style.opacity = '0';
                setTimeout(function () { toast.remove(); }, 300);
            }, 3000);
        }
    });
    </script>
    @endcan
</x-app-layout>
