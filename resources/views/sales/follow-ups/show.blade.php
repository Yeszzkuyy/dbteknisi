<x-app-layout>
    <div >
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Detail Follow Up</h1>
                <p class="text-slate-500 mt-1">{{ $followUp->customer?->name ?? '-' }}</p>
            </div>
            <div class="flex gap-2">
                <x-icon-button as="a" href="{{ route('sales.meetings.create', array_filter(['customer_id' => $followUp->customer_id, 'lead_id' => $followUp->lead_id])) }}" title="Create Meeting">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15.5" rx="2" /><path d="M3.5 10h17" /><path d="M8 3v4" /><path d="M16 3v4" /></svg>
                </x-icon-button>
                @can('manage-sales')
                    <a href="{{ route('sales.follow-ups.edit', $followUp) }}"
                       class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition">
                        Edit
                    </a>
                @endcan
                <a href="{{ route('sales.follow-ups.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-accent-500 text-white hover:bg-accent-600 dark:bg-accent-600 dark:hover:bg-accent-700 text-sm font-medium transition">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-xs text-slate-400">Customer</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->customer?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Jenis') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->type ? \App\Models\FollowUp::typeLabel($followUp->type) : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Tanggal Follow Up') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->follow_up_date ? $followUp->follow_up_date->format('d M Y') : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Next Follow Up') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->next_follow_up_date ? $followUp->next_follow_up_date->format('d M Y') : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Terkait Lead') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->lead?->customer?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Terkait Meeting') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->meeting ? $followUp->meeting->meeting_date->format('d M Y') : __('Tidak') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">{{ __('Dicatat oleh') }}</dt>
                    <dd class="font-medium text-slate-800">{{ $followUp->creator?->name ?? '-' }}</dd>
                </div>
            </dl>

            <div>
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('Deskripsi') }}</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $followUp->description }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
