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

    {{-- Daily Update (AI draft → approve) --}}
    @can('manage-sales')
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 mb-6">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ __('Daily Update') }}</h2>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">{{ __('Type one sentence, AI composes the draft from today follow-ups and history. Review before approving.') }}</p>

            <form method="POST" action="{{ route('sales.meeting-drafts.generate') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Customer') }}</label>
                    <x-searchable-select
                        name="customer_id"
                        :options="($customers ?? collect())->mapWithKeys(fn ($c) => [$c->id => $c->name])->all()"
                        :selected="old('customer_id')"
                        placeholder="{{ __('Type customer name to search...') }}"
                    />
                    @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Related Lead (optional)') }}</label>
                    <select name="lead_id" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                        <option value="">{{ __('No specific lead') }}</option>
                        @foreach($leads ?? [] as $lead)
                            <option value="{{ $lead->id }}" @selected(old('lead_id') == $lead->id)>
                                {{ $lead->customer->name ?? 'Lead #'.$lead->id }} — {{ ucfirst($lead->status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('What happened today? (one sentence)') }}</label>
                    <div class="flex items-start gap-2">
                        <textarea name="source_sentence" rows="2" required maxlength="500"
                                  placeholder="{{ __('e.g. Visited PT Maju, demo CCTV 8 channels, they asked for a revised quote') }}"
                                  class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">{{ old('source_sentence') }}</textarea>
                        <x-icon-button icon="add" type="submit" title="Generate Draft" />
                    </div>
                    @error('source_sentence') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </form>

            @if(($drafts ?? collect())->isNotEmpty())
                <div class="mt-5 space-y-4 border-t border-slate-100 dark:border-slate-700 pt-4">
                    @foreach($drafts as $draft)
                        <form method="POST" action="{{ route('sales.meeting-drafts.approve', $draft) }}" class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/10 p-4">
                            @csrf
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $draft->customer?->name ?? '-' }}
                                        <span class="ml-1 font-normal text-xs text-slate-500">{{ $draft->meeting_date?->format('d M Y') }}</span>
                                        @if($draft->lead)<span class="ml-1 font-normal text-xs text-slate-500">• {{ ucfirst($draft->lead->status) }}</span>@endif
                                        <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">{{ __('AI Draft') }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500 italic mt-0.5">“{{ $draft->source_sentence }}”</p>
                                </div>
                                <button type="submit" formaction="{{ route('sales.meeting-drafts.discard', $draft) }}"
                                        class="shrink-0 text-xs text-slate-400 hover:text-red-500 transition"
                                        onclick="return confirm('{{ __('Discard this draft?') }}')">{{ __('Discard') }}</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Participants') }}</label>
                                    <input type="text" name="participants" value="{{ $draft->participants }}" maxlength="500"
                                           class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('User Needs') }}</label>
                                    <textarea name="user_needs" rows="2" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">{{ $draft->user_needs }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('User Complaints') }}</label>
                                    <textarea name="user_complaints" rows="2" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">{{ $draft->user_complaints }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Notes') }}</label>
                                    <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500 text-sm">{{ $draft->notes }}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="existing_system" value="{{ $draft->existing_system }}">
                            <div class="mt-3 flex justify-end">
                                <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-accent-600 hover:bg-accent-500 text-white text-sm font-medium transition-all duration-300 hover:scale-105 active:scale-95">
                                    {{ __('Approve & Save Meeting') }}
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>
    @endcan

    {{-- Table --}}
    @include('sales.meetings._table')
</x-app-layout>
