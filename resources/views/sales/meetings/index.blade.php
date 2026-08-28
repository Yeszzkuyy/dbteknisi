<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Tracker Meeting Customer</h1>
            <p class="text-slate-500 mt-1">Catat dan pantau seluruh meeting dengan customer.</p>
        </div>
        @can('manage-sales')
            <a href="{{ route('sales.meetings.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                + Catat Meeting
            </a>
        @endcan
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Cari Customer</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama customer..."
                       class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
                <x-datepicker name="date_from" value="{{ request('date_from') }}"></x-datepicker>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                <x-datepicker name="date_to" value="{{ request('date_to') }}"></x-datepicker>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                    <a href="{{ route('sales.meetings.index') }}"
                       class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Peserta</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Kebutuhan (ringkas)</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Follow Up</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $meeting->customer->name }}</div>
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
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('sales.meetings.show', $meeting) }}"
                                       class="text-blue-600 hover:text-blue-800">Detail</a>
                                    @can('manage-sales')
                                        <a href="{{ route('sales.meetings.edit', $meeting) }}"
                                           class="text-amber-600 hover:text-amber-800">Edit</a>
                                        <form action="{{ route('sales.meetings.destroy', $meeting) }}"
                                              method="POST" onsubmit="return confirm('Hapus meeting ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">Belum ada meeting.</td>
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
</x-app-layout>
