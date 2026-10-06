<x-app-layout>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ __('Sales Schedule') }}</h1>
                <p class="text-slate-500 mt-1">{{ __('Agenda sales & inside sales.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-icon-button as="a" icon="back" href="{{ route('sales.dashboard') }}" title="{{ __('Back') }}" />
                @can('manage-sales')
                    <x-icon-button as="a" icon="add" href="{{ route('sales.schedules.create') }}" title="{{ __('Add') }}" />
                @endcan
                @if(auth()->user()?->can('manage-inside-sales'))
                    <x-icon-button as="a" icon="add" href="{{ route('sales.schedules.create') }}" title="{{ __('Add') }}" />
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="{{ route('sales.schedules.index', ['tab' => 'upcoming'] + request()->only('type')) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'upcoming' ? 'bg-accent-600 text-white' : 'bg-white text-slate-600 border border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600' }}">{{ __('Upcoming') }}</a>
            <a href="{{ route('sales.schedules.index', ['tab' => 'past'] + request()->only('type')) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'past' ? 'bg-accent-600 text-white' : 'bg-white text-slate-600 border border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600' }}">{{ __('Past') }}</a>
            <form method="GET" action="{{ route('sales.schedules.index') }}" class="flex items-center gap-2 ml-auto">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <select name="type" onchange="this.form.submit()"
                        class="rounded-xl border-slate-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:bg-slate-800 dark:border-slate-600">
                    <option value="">{{ __('Semua tipe') }}</option>
                    @foreach(\App\Models\SalesSchedule::TYPES as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ \App\Models\SalesSchedule::typeLabel($type) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4">
            @forelse($schedules as $schedule)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-700' : '' }}">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-blue-100 text-blue-800">
                        {{ \App\Models\SalesSchedule::typeLabel($schedule->type) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate">{{ $schedule->title }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $schedule->start_at?->format('d M Y H:i') ?? '-' }}
                            • {{ $schedule->lead->customer?->name ?? 'Lead #'.$schedule->lead_id }}
                            • {{ __('PIC') }}: {{ $schedule->assignee?->name ?? '-' }}
                        </p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold shrink-0 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200">
                        {{ \App\Models\SalesSchedule::statusLabel($schedule->status) }}
                    </span>
                    <a href="{{ route('sales.schedules.show', $schedule) }}" title="{{ __('View details') }}" aria-label="{{ __('View details') }}"
                       class="h-10 w-10 inline-flex items-center justify-center rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </a>
                </div>
            @empty
                <p class="text-slate-500 text-center py-8">{{ __('Belum ada jadwal.') }}</p>
            @endforelse
            <div class="mt-4">{{ $schedules->links() }}</div>
        </div>
    </div>
</x-app-layout>
