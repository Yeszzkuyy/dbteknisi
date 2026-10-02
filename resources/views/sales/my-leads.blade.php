<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Leads</h1>
            <p class="text-slate-500 mt-1">{{ __('Leads assigned to you by Management') }}</p>
        </div>
        <div class="flex gap-2">
            <x-icon-button as="a" icon="import" href="{{ route('sales.my-leads.export', request()->only(['search', 'status', 'touched', 'active', 'won_month', 'sort'])) }}" title="Export CSV" />
            <x-icon-button as="a" icon="back" href="{{ route('sales.dashboard') }}" title="Dashboard Sales" />
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">
        <div class="p-5 border-b dark:border-slate-600 bg-slate-50 dark:bg-slate-700 rounded-t-2xl">
            <form method="GET" action="{{ route('sales.my-leads') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @if(request()->filled('active'))
                    <input type="hidden" name="active" value="{{ request('active') }}">
                @endif
                @if(request()->filled('won_month'))
                    <input type="hidden" name="won_month" value="{{ request('won_month') }}">
                @endif
                <div>
                    <label for="my-leads-search" class="text-sm font-medium text-slate-500">{{ __('Search customer') }}</label>
                    <input id="my-leads-search" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search customer...') }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-accent-500 focus:ring-accent-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">{{ __('Status') }}</label>
                    <x-glide-select name="status" class="mt-1" :label="__('Status')"
                        :options="collect($statuses ?? [])->map(fn ($s) => ['value' => $s, 'label' => ucfirst($s)])->all()"
                        :value="request('status', '')" :empty-label="__('All Statuses')" autosubmit />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">{{ __('Activity') }}</label>
                    <x-glide-select name="touched" class="mt-1" :label="__('Activity')"
                        :options="[['value' => 'yes', 'label' => __('Touched')], ['value' => 'no', 'label' => __('Untouched')]]"
                        :value="request('touched', '')" :empty-label="__('All Activity')" autosubmit />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">{{ __('Sort by') }}</label>
                    <x-glide-select name="sort" class="mt-1" :label="__('Sort by')"
                        :options="[['value' => 'oldest', 'label' => __('Oldest')], ['value' => 'customer', 'label' => __('Customer A-Z')]]"
                        :value="request('sort', '')" :empty-label="__('Newest')" autosubmit />
                </div>
                <div class="sm:col-span-2 lg:col-span-1 flex items-end gap-2">
                    <x-icon-button icon="filter" type="submit" title="Filter" />
                    @if(request('search') || request('status') || request('touched') || request('active') || request('won_month') || request('sort'))
                        <x-icon-button as="a" icon="reset" href="{{ route('sales.my-leads') }}" title="Reset" />
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Lead / Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Needs / Progress') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">{{ __('Entry Date') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">{{ __('Actions') }}</th>
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
                                <div class="mt-1">
                                    @if($lead->has_done_fu)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">{{ __('Done') }}</span>
                                    @else
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">{{ __('Pending FU') }}</span>
                                    @endif
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
                                        <p class="mt-1 text-xs text-slate-500 line-clamp-1" title="{{ $lead->solusi }}">{{ __('Solution') }}: {{ $lead->solusi }}</p>
                                    @endif
                                    @if($lead->progress_notes)
                                        <p class="mt-1 text-xs text-slate-500 line-clamp-1" title="{{ $lead->progress_notes }}">{{ __('Progress') }}: {{ $lead->progress_notes }}</p>
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
                                        __('Hi :name, may I follow up on our proposal.', ['name' => $lead->customer?->contact_person ?: ($lead->customer?->name ?? '')])
                                    );
                                @endphp
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('leads.show', $lead) }}"
                                       title="{{ __('View details') }}"
                                       aria-label="{{ __('View details') }} {{ $lead->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    @if($leadWaLink)
                                        <a href="{{ $leadWaLink }}" target="_blank"
                                           title="{{ __('Chat on WhatsApp') }}"
                                           aria-label="{{ __('Chat on WhatsApp') }} {{ $lead->customer?->name }}"
                                           class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition dark:bg-green-500/10 dark:text-green-300 dark:hover:bg-green-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4" aria-hidden="true">
                                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m5.83 14.12c-.25.7-1.45 1.33-2.02 1.42-.52.08-1.17.12-1.89-.12-.44-.15-1-.39-1.71-.75-3.03-1.39-5-4.63-5.15-4.84-.15-.21-1.23-1.64-1.23-3.13 0-1.49.78-2.22 1.06-2.52.28-.3.61-.38.81-.38l.58.01c.19.01.44-.07.69.53.25.61.86 2.11.94 2.26.08.15.13.33.03.53-.1.2-.15.33-.3.51l-.45.53c-.15.15-.31.31-.13.61.18.3.8 1.32 1.71 2.14 1.18 1.06 2.17 1.39 2.48 1.55.3.15.48.13.66-.08l1.1-1.28c.2-.26.42-.22.71-.13.3.09 1.9.9 2.23 1.06.38.2.53.44.5.65-.27.69-.52.98-.73 1.23"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($lead->has_done_fu)
                                        <span title="{{ __('Done') }}" aria-label="{{ __('Done') }} {{ $lead->customer?->name }}"
                                              class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-300">
                                            <x-icon name="check-circle" class="h-4 w-4" />
                                        </span>
                                    @else
                                        <a href="{{ route('sales.follow-ups.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}"
                                           title="{{ __('Create Follow Up') }}"
                                           aria-label="{{ __('Create Follow Up') }} {{ $lead->customer?->name }}"
                                           class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-700 transition dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20">
                                            <x-icon name="chat" class="h-4 w-4" />
                                        </a>
                                    @endif
                                    <a href="{{ route('sales.meetings.create', ['customer_id' => $lead->customer_id, 'lead_id' => $lead->id]) }}"
                                       title="{{ __('Create Meeting') }}"
                                       aria-label="{{ __('Create Meeting') }} {{ $lead->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20">
                                        <x-icon name="calendar" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300">
                                    <x-icon name="briefcase" class="h-6 w-6" />
                                </span>
                                <p class="mt-4 text-sm font-medium text-slate-500">{{ __('No leads assigned to you yet.') }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ __('New leads from Management will appear here.') }}</p>
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
