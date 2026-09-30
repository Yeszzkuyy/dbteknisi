<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Follow Up Customer</h1>
            <p class="text-slate-500 mt-1">{{ __('Pantau tindak lanjut dengan customer.') }}</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
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
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-accent-600 hover:bg-accent-700 text-white text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'overdue']))
                    <a href="{{ route('sales.follow-ups.index') }}" data-ajax-reset
                       class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    @include('sales.follow-ups._table')
</x-app-layout>
