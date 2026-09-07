<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">My Leads</h1>
            <p class="text-slate-500 mt-1">Lead yang di-assign Management kepada Anda</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Lead / Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Kebutuhan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Solusi</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Progress FollowUp</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Tanggal Masuk</th>
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
                            </td>
                            <td class="px-6 py-4">
                                @if($lead->kebutuhan)
                                    <span class="text-sm text-slate-700 truncate max-w-[200px] inline-block align-bottom" title="{{ $lead->kebutuhan }}">{{ Str::limit($lead->kebutuhan, 40) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($lead->solusi)
                                    <span class="text-sm text-slate-700 truncate max-w-[200px] inline-block align-bottom" title="{{ $lead->solusi }}">{{ Str::limit($lead->solusi, 40) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($lead->progress_notes)
                                    <span class="text-sm text-slate-700 truncate max-w-[200px] inline-block align-bottom" title="{{ $lead->progress_notes }}">{{ Str::limit($lead->progress_notes, 40) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($lead->incoming_date)
                                    <span class="text-sm text-slate-700">{{ $lead->incoming_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Belum ada lead yang di-assign kepada Anda.
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