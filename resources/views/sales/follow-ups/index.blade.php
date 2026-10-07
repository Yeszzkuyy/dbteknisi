<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Follow Up & Meeting') }}</h1>
            <p class="text-slate-500 mt-1">{{ __('Pantau meeting dan tindak lanjut dengan customer.') }}</p>
        </div>
        @can('manage-sales')
            <x-icon-button as="a" icon="add" href="{{ route('sales.follow-ups.create') }}" title="Add Follow Up" />
        @endcan
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 mb-6">
        <form method="GET" data-ajax data-ajax-target="#followups-table" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Cari Customer') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Nama customer...') }}"
                       class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <select name="overdue" onchange="this.form.requestSubmit()"
                            class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('Semua') }}</option>
                        <option value="today" @selected(request('overdue') === 'today')>{{ __('Hari ini') }}</option>
                        <option value="upcoming" @selected(request('overdue') === 'upcoming')>{{ __('Mendatang') }}</option>
                        <option value="1" @selected(request('overdue') === '1')>{{ __('Jatuh tempo') }}</option>
                    </select>
            </div>
            <div class="flex items-end gap-2">
                <x-icon-button icon="filter" type="submit" title="Filter" />
                @if(request()->anyFilled(['search', 'overdue']))
                    <x-icon-button as="a" icon="reset" href="{{ route('sales.follow-ups.index') }}" title="Reset" data-ajax-reset />
                @endif
            </div>
        </form>
    </div>

    {{-- Meetings section --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-800">{{ __('Meetings') }}</h2>
            @can('manage-sales')
                <x-icon-button as="a" icon="add" href="{{ route('sales.meetings.create') }}" title="Add Meeting" />
            @endcan
        </div>
        @include('sales.meetings._table', ['meetings' => $meetings ?? []])
    </div>

    {{-- Follow Ups section --}}
    <div>
        <h2 class="text-lg font-semibold text-slate-800 mb-4">{{ __('Follow Ups') }}</h2>
        @include('sales.follow-ups._table')
    </div>
</x-app-layout>
