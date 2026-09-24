{{-- Partial tabel: dipakai full view + refresh AJAX filter (tanpa reload). --}}
<div id="meetings-table" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Tanggal') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Peserta') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Kebutuhan (ringkas)') }}</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Follow Up</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                @forelse($meetings as $meeting)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $meeting->customer?->name ?? '-' }}</div>
                            <div class="text-xs text-slate-400">oleh {{ $meeting->creator?->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            {{ $meeting->meeting_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $meeting->participants ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">
                            {{ Str::limit($meeting->user_needs, 80) ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                {{ $meeting->followUps->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $meeting->followUps->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('sales.meetings.show', $meeting) }}"
                                   title="{{ __('Lihat detail meeting') }}"
                                   aria-label="{{ __('Lihat detail meeting') }} {{ $meeting->customer?->name }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-accent-50 text-accent-700 transition hover:bg-accent-100 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                @can('manage-sales')
                                    <a href="{{ route('sales.meetings.edit', $meeting) }}"
                                       title="{{ __('Edit meeting') }}"
                                       aria-label="{{ __('Edit meeting') }} {{ $meeting->customer?->name }}"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-accent-100 text-accent-700 transition hover:bg-accent-200 dark:bg-accent-500/10 dark:text-accent-300 dark:hover:bg-accent-500/20">
                                        <x-icon name="edit" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('sales.meetings.destroy', $meeting) }}"
                                          method="POST" class="inline-flex" data-ajax data-ajax-remove="tr" onsubmit="return confirm('{{ __('Hapus meeting ini?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                title="{{ __('Hapus meeting') }}"
                                                aria-label="{{ __('Hapus meeting') }} {{ $meeting->customer?->name }}"
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
                        <td colspan="6" class="py-16 text-center text-slate-400">{{ __('Belum ada meeting.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($meetings->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $meetings->links() }}
        </div>
    @endif
</div>
