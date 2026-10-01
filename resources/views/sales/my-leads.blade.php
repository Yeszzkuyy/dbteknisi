<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Leads</h1>
            <p class="text-slate-500 mt-1">{{ __('Lead yang di-assign Management kepada Anda') }}</p>
        </div>
        <div class="flex gap-2">
            <x-icon-button as="a" icon="import" href="{{ route('sales.my-leads.export', request()->only(['search', 'status', 'touched', 'active', 'won_month', 'sort'])) }}" title="Export CSV" />
            <x-icon-button as="a" icon="back" href="{{ route('sales.dashboard') }}" title="Dashboard Sales" />
        </div>
    </div>

    <form method="GET" action="{{ route('sales.my-leads') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="w-full sm:w-auto sm:flex-1 sm:min-w-48 sm:max-w-xs">
            <label for="my-leads-search" class="block text-xs font-medium text-slate-500 mb-1">{{ __('Cari customer') }}</label>
            <input id="my-leads-search" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari customer...') }}"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
        </div>
        <div>
            <label for="my-leads-status" class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
            <select id="my-leads-status" name="status" onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                <option value="">{{ __('Semua Status') }}</option>
                @foreach($statuses ?? [] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="my-leads-touched" class="block text-xs font-medium text-slate-500 mb-1">{{ __('Aktivitas') }}</label>
            <select id="my-leads-touched" name="touched" onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                <option value="">{{ __('Semua Aktivitas') }}</option>
                <option value="yes" @selected(request('touched') === 'yes')>{{ __('Sudah disentuh') }}</option>
                <option value="no" @selected(request('touched') === 'no')>{{ __('Belum disentuh') }}</option>
            </select>
        </div>
        <div>
            <label for="my-leads-sort" class="block text-xs font-medium text-slate-500 mb-1">{{ __('Urutkan') }}</label>
            <select id="my-leads-sort" name="sort" onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                <option value="">{{ __('Terbaru') }}</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Terlama') }}</option>
                <option value="customer" @selected(request('sort') === 'customer')>{{ __('Customer A-Z') }}</option>
            </select>
        </div>
        <div class="flex gap-2">
            <x-icon-button icon="filter" type="submit" title="Filter" />
            @if(request('search') || request('status') || request('touched') || request('active') || request('won_month') || request('sort'))
                <x-icon-button as="a" icon="reset" href="{{ route('sales.my-leads') }}" title="Reset" />
            @endif
        </div>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Lead / Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Kebutuhan / Progres') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">{{ __('Tanggal Masuk') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $lead->customer->name ?? 'N/A' }}</div>
                                <div class="text-sm text-slate-500">
                                    @if($lead->pt_group)<span class="inline-flex px-1.5 py-0.5 rounded {{ \App\Models\Lead::PT_COLORS[$lead->pt_group] ?? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' }} text-[11px] font-semibold mr-1">{{ $lead->pt_group }}</span>@endif
                                    {{ $lead->customer->contact_person ? 'PIC: '.$lead->customer->contact_person : '' }}
                                </div>
                                <div class="mt-1 text-[11px] text-slate-500 whitespace-nowrap">
                                    {{ $lead->meetings_count }} {{ __('meeting') }} • {{ $lead->follow_ups_count }} {{ __('follow up') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                    @switch($lead->status)
                                        @case('new') bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 @break
                                        @case('contacted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 @break
                                        @case('qualified') bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 @break
                                        @case('proposal') bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300 @break
                                        @case('won') bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 @break
                                        @case('lost') bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 @break
                                        @default bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200
                                    @endswitch
                                ">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="min-w-[220px] max-w-[340px]">
                                    @if($lead->kebutuhan)
                                        <p class="text-sm text-slate-700 line-clamp-2" title="{{ $lead->kebutuhan }}">{{ $lead->kebutuhan }}</p>
                                    @endif
                                    @if($lead->solusi)
                                        <p class="mt-1 text-xs text-slate-500 line-clamp-1" title="{{ $lead->solusi }}">{{ __('Solusi') }}: {{ $lead->solusi }}</p>
                                    @endif
                                    @if($lead->progress_notes)
                                        <p class="mt-1 text-xs text-slate-500 line-clamp-1" title="{{ $lead->progress_notes }}">{{ __('Progres') }}: {{ $lead->progress_notes }}</p>
                                    @endif
                                    @if(!$lead->kebutuhan && !$lead->solusi && !$lead->progress_notes)
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($lead->incoming_date)
                                    <span class="text-sm text-slate-700">{{ $lead->incoming_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @php
                                    $leadWaLink = $lead->customer?->waLink(
                                        __('Halo :name, izin follow up penawaran kami.', ['name' => $lead->customer?->contact_person ?: ($lead->customer?->name ?? '')])
                                    );
                                @endphp
                                <a href="{{ route('leads.show', $lead) }}"
                                   title="{{ __('View details') }}"
                                   aria-label="{{ __('View details') }} {{ $lead->customer?->name }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                <x-dropdown align="right" width="w-52">
                                    <x-slot name="trigger">
                                        <x-icon-button title="{{ __('More actions') }}" tooltip="">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><circle cx="5" cy="12" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="19" cy="12" r="1.6" /></svg>
                                        </x-icon-button>
                                    </x-slot>
                                    <x-slot name="content">
                                        @if($leadWaLink)
                                            <x-dropdown-link href="{{ $leadWaLink }}" target="_blank">{{ __('Chat WhatsApp') }}</x-dropdown-link>
                                        @endif
                                        <x-dropdown-link href="{{ route('sales.meetings.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}">{{ __('Buat Meeting') }}</x-dropdown-link>
                                        <x-dropdown-link href="{{ route('sales.follow-ups.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}">{{ __('Buat Follow Up') }}</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300">
                                    <x-icon name="briefcase" class="h-6 w-6" />
                                </span>
                                <p class="mt-4 text-sm font-medium text-slate-500">{{ __('Belum ada lead yang di-assign kepada Anda.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('Lead baru dari Management akan muncul di sini.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $leads->links() }}
        </div>
    </div>
</x-app-layout>
