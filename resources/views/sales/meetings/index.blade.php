<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ __('Tracker Meeting Customer') }}</h1>
            <p class="text-slate-500 mt-1">{{ __('Catat dan pantau seluruh meeting dengan customer.') }}</p>
        </div>
        @can('manage-sales')
            <a href="{{ route('sales.meetings.create') }}"
               class="group relative overflow-hidden px-5 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-500 text-white font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-accent-500/40 hover:brightness-110 active:scale-95">
                <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-full" aria-hidden="true"></span>
                {{ __('+ Catat Meeting') }}
            </a>
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
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2">
                    {{ __('Filter') }}
                </button>
                @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                    <a href="{{ route('sales.meetings.index') }}" data-ajax-reset
                       class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    @include('sales.meetings._table')
</x-app-layout>
