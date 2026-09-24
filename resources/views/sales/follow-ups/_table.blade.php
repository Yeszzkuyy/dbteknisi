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
            <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                @forelse($followUps as $fu)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800">{{ $fu->customer?->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                            {{ Str::limit($fu->description, 100) }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $fu->meeting ? $fu->meeting->meeting_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $fu->follow_up_date ? $fu->follow_up_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $fu->creator?->name ?? '-' }}</td>
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
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-accent-50 text-accent-700 transition hover:bg-accent-100 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                @can('manage-sales')
                                    <a href="{{ route('sales.follow-ups.edit', $fu) }}"
                                       title="{{ __('Edit follow up') }}"
                                       aria-label="{{ __('Edit follow up') }} {{ $fu->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-accent-100 text-accent-700 transition hover:bg-accent-200 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
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
                        <td colspan="6" class="py-16 text-center text-slate-400">{{ __('Belum ada follow up.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($followUps->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $followUps->links() }}
        </div>
    @endif
</div>
