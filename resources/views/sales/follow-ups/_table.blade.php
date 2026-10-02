{{-- Partial tabel: dipakai full view + refresh AJAX filter (tanpa reload). --}}
<div id="followups-table" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Deskripsi') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Terkait Meeting') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Tanggal Follow Up') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Oleh') }}</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
                @forelse($followUps as $fu)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition {{ $fu->completed_at ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $fu->customer?->name ?? '-' }}</span>
                            @if($fu->type)
                                <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ \App\Models\FollowUp::typeLabel($fu->type) }}</span>
                            @endif
                            @if($fu->completed_at)
                                <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">{{ __('Done') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-xs truncate">
                            {{ Str::limit($fu->description, 100) }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            {{ $fu->meeting ? $fu->meeting->meeting_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            {{ $fu->follow_up_date ? $fu->follow_up_date->format('d M Y') : '-' }}
                            @if(!$fu->completed_at && $fu->follow_up_date && $fu->follow_up_date->isBefore(today()))
                                <span class="ml-1 inline-flex px-1.5 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ __('Terlambat') }}</span>
                            @endif
                            @if($fu->next_follow_up_date)
                                <span class="block mt-1 text-[11px] text-slate-500">{{ __('Berikutnya:') }} {{ $fu->next_follow_up_date->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $fu->creator?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                @php
                                    $fuWaMessage = 'Halo ' . ($fu->customer?->contact_person ?: ($fu->customer?->name ?? ''))
                                        . ', izin follow up "' . Str::limit($fu->description, 60) . '".';
                                    $fuWaLink = $fu->customer?->waLink($fuWaMessage);
                                @endphp
                                @if($fuWaLink)
                                    <a href="{{ $fuWaLink }}" target="_blank"
                                       title="{{ __('Follow up via WhatsApp') }}"
                                       aria-label="{{ __('Follow up via WhatsApp') }} {{ $fu->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition dark:bg-green-500/10 dark:text-green-300 dark:hover:bg-green-500/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m5.83 14.12c-.25.7-1.45 1.33-2.02 1.42-.52.08-1.17.12-1.89-.12-.44-.15-1-.39-1.71-.75-3.03-1.39-5-4.63-5.15-4.84-.15-.21-1.23-1.64-1.23-3.13 0-1.49.78-2.22 1.06-2.52.28-.3.61-.38.81-.38l.58.01c.19.01.44-.07.69.53.25.61.86 2.11.94 2.26.08.15.13.33.03.53-.1.2-.15.33-.3.51l-.45.53c-.15.15-.31.31-.13.61.18.3.8 1.32 1.71 2.14 1.18 1.06 2.17 1.39 2.48 1.55.3.15.48.13.66-.08l1.1-1.28c.2-.26.42-.22.71-.13.3.09 1.9.9 2.23 1.06.38.2.53.44.5.65-.27.69-.52.98-.73 1.23"/>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('sales.follow-ups.show', $fu) }}"
                                   title="{{ __('Lihat detail follow up') }}"
                                   aria-label="{{ __('Lihat detail follow up') }} {{ $fu->customer?->name }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 transition hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('sales.meetings.create', array_filter(['customer_id' => $fu->customer_id, 'lead_id' => $fu->lead_id])) }}"
                                   title="{{ __('Create Meeting') }}"
                                   aria-label="{{ __('Create Meeting') }} {{ $fu->customer?->name }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20">
                                    <x-icon name="calendar" class="h-4 w-4" />
                                </a>
                                @can('manage-sales')
                                    @if($fu->completed_at)
                                        <form action="{{ route('sales.follow-ups.reopen', $fu) }}"
                                              method="POST" class="inline-flex" data-ajax>
                                            @csrf
                                            <button type="submit"
                                                    title="{{ __('Reopen follow up') }}"
                                                    aria-label="{{ __('Reopen follow up') }} {{ $fu->customer?->name }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20">
                                                <x-icon name="restore" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('sales.follow-ups.complete', $fu) }}"
                                              method="POST" class="inline-flex" data-ajax>
                                            @csrf
                                            <button type="submit"
                                                    title="{{ __('Mark as done') }}"
                                                    aria-label="{{ __('Mark as done') }} {{ $fu->customer?->name }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-700 transition hover:bg-green-200 dark:bg-green-500/10 dark:text-green-300 dark:hover:bg-green-500/20">
                                                <x-icon name="check-circle" class="h-4 w-4" />
                                            </button>
                                        </form>
                                        <x-dropdown align="right" width="w-44">
                                            <x-slot name="trigger">
                                                <button type="button"
                                                        title="{{ __('Snooze follow up') }}"
                                                        aria-label="{{ __('Snooze follow up') }} {{ $fu->customer?->name }}"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700 transition hover:bg-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-300 dark:hover:bg-yellow-500/20">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true"><circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" d="M12 7.5V12l3 2" /></svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @foreach([1, 3, 7] as $days)
                                                    <form action="{{ route('sales.follow-ups.snooze', $fu) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="days" value="{{ $days }}">
                                                        <button type="submit"
                                                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus:bg-slate-700 transition duration-150 ease-in-out">
                                                            {{ $days === 1 ? __('Tomorrow') : __('+ :days days', ['days' => $days]) }}
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                    <a href="{{ route('sales.follow-ups.edit', $fu) }}"
                                       title="{{ __('Edit follow up') }}"
                                       aria-label="{{ __('Edit follow up') }} {{ $fu->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700 transition hover:bg-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('sales.follow-ups.destroy', $fu) }}"
                                          method="POST" class="inline-flex" data-ajax data-ajax-remove="tr" onsubmit="return confirm('{{ __('Hapus follow up ini?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                title="{{ __('Hapus follow up') }}"
                                                aria-label="{{ __('Hapus follow up') }} {{ $fu->customer?->name }}"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300">
                                <x-icon name="chat" class="h-6 w-6" />
                            </span>
                            <p class="mt-4 text-sm font-medium text-slate-500">{{ __('Belum ada follow up.') }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ __('Catat follow up pertama dari tombol di atas.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($followUps->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-600">
            {{ $followUps->links() }}
        </div>
    @endif
</div>
