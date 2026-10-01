<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Tracker Meeting Customer') }}</h1>
            <p class="text-slate-500 mt-1">{{ __('Catat dan pantau seluruh meeting dengan customer.') }}</p>
        </div>
        @can('manage-sales')
            <x-icon-button as="a" icon="add" href="{{ route('sales.meetings.create') }}" title="Add Meeting" />
        @endcan
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 mb-6">
        <form method="GET" data-ajax data-ajax-target="#meetings-table" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Cari Customer') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('Nama customer...') }}"
                       class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Dari Tanggal') }}</label>
                <x-datepicker name="date_from" value="{{ request('date_from') }}"></x-datepicker>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Sampai Tanggal') }}</label>
                <x-datepicker name="date_to" value="{{ request('date_to') }}"></x-datepicker>
            </div>
            <div class="flex items-end gap-2">
                <x-icon-button icon="filter" type="submit" title="Filter" />
                @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                    <x-icon-button as="a" icon="reset" href="{{ route('sales.meetings.index') }}" title="Reset" data-ajax-reset />
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    @include('sales.meetings._table')
</x-app-layout>
