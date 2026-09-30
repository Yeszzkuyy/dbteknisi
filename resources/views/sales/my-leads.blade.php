<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Leads</h1>
            <p class="text-slate-500 mt-1">{{ __('Lead yang di-assign Management kepada Anda') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Lead Aktif') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpi['active'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <x-icon name="briefcase" class="h-5 w-5" />
                </span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Won Bulan Ini') }}</p>
                    <p class="mt-1 text-3xl font-bold text-green-700 dark:text-green-300">{{ $kpi['won_month'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                    <x-icon name="check-circle" class="h-5 w-5" />
                </span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Meeting Minggu Ini') }}</p>
                    <p class="mt-1 text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $kpi['meetings_week'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    <x-icon name="calendar" class="h-5 w-5" />
                </span>
            </div>
        </div>
        <a href="{{ route('sales.follow-ups.index', ['overdue' => 1]) }}"
           class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 hover:shadow transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Follow Up Jatuh Tempo') }}</p>
                    <p class="mt-1 text-3xl font-bold {{ ($kpi['overdue'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $kpi['overdue'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <x-icon name="phone" class="h-5 w-5" />
                </span>
            </div>
        </a>
        <a href="{{ route('sales.follow-ups.index', ['overdue' => 'today']) }}"
           class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 hover:shadow transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Follow Up Hari Ini') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpi['followups_today'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                    <x-icon name="chat" class="h-5 w-5" />
                </span>
            </div>
        </a>
        <a href="{{ route('sales.follow-ups.index', ['overdue' => 'upcoming']) }}"
           class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 hover:shadow transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Follow Up Mendatang') }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $kpi['followups_upcoming'] ?? 0 }}</p>
                </div>
                <span class="shrink-0 rounded-xl p-2.5 bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                    <x-icon name="activity" class="h-5 w-5" />
                </span>
            </div>
        </a>
    </div>

    @if(($dueFollowUps ?? collect())->isNotEmpty() || ($weekMeetings ?? collect())->isNotEmpty() || ($myTasks ?? collect())->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="shrink-0 rounded-lg p-2 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                        <x-icon name="phone" class="h-4 w-4" />
                    </span>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('Follow Up Mendesak') }}</h3>
                </div>
                @forelse($dueFollowUps as $fu)
                    <a href="{{ route('sales.follow-ups.show', $fu) }}" class="block py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:underline min-w-0">
                        <span class="block text-sm font-medium text-slate-800 dark:text-slate-100 truncate">{{ $fu->customer?->name ?? '-' }}</span>
                        <span class="block text-xs text-slate-500">{{ $fu->follow_up_date?->format('d M Y') ?? '-' }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">{{ __('Tidak ada.') }}</p>
                @endforelse
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="shrink-0 rounded-lg p-2 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                        <x-icon name="calendar" class="h-4 w-4" />
                    </span>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('Meeting Minggu Ini') }}</h3>
                </div>
                @forelse($weekMeetings as $meeting)
                    <a href="{{ route('sales.meetings.show', $meeting) }}" class="block py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:underline min-w-0">
                        <span class="block text-sm font-medium text-slate-800 dark:text-slate-100 truncate">{{ $meeting->customer?->name ?? '-' }}</span>
                        <span class="block text-xs text-slate-500">{{ $meeting->meeting_date?->format('d M Y') ?? '-' }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">{{ __('Tidak ada.') }}</p>
                @endforelse
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                    <span class="shrink-0 rounded-lg p-2 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <x-icon name="file-text" class="h-4 w-4" />
                    </span>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('Inside Sales Task Aktif') }}</h3>
                </div>
                @forelse($myTasks as $task)
                    <a href="{{ route('lead-tasks.show', $task) }}" class="block py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:underline min-w-0">
                        <span class="block text-sm font-medium text-slate-800 dark:text-slate-100 truncate">{{ $task->title }}</span>
                        <span class="block text-xs text-slate-500 truncate">{{ $task->assignee?->name ?? __('Belum di-assign') }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">{{ __('Tidak ada.') }}</p>
                @endforelse
            </div>
        </div>
    @endif

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
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">{{ __('Cari') }}</button>
            @if(request('search') || request('status') || request('touched'))
                <a href="{{ route('sales.my-leads') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 text-sm rounded-lg transition dark:border-slate-600 dark:text-slate-200">{{ __('Reset') }}</a>
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
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold transition">
                                    {{ __('Detail') }}
                                </a>
                                <x-dropdown align="right" width="w-52">
                                    <x-slot name="trigger">
                                        <button type="button" aria-label="{{ __('Aksi lainnya') }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500">
                                            {{ __('Lainnya') }}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
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
