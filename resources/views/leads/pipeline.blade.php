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
</x-app-layout>
