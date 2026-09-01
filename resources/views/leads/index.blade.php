<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Daftar Lead / Opportunity</h1>
            <p class="text-slate-500 mt-1">Kelola lead marketing dan opportunity sales</p>
        </div>
        @can('manage-marketing')
            <div class="flex gap-3">
                <a href="{{ route('leads.import') }}"
                   class="px-5 py-2.5 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">
                    📥 Import
                </a>
                <a href="{{ route('leads.create') }}"
                   class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    + Tambah Lead
                </a>
            </div>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">
        <div class="p-5 border-b dark:border-slate-600 bg-slate-50 dark:bg-slate-700 rounded-t-2xl">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Cari Customer</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama customer..."
                           class="w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm px-3 py-2 focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200 dark:focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                    <select name="status" class="w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm px-3 py-2 focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200 dark:focus:border-blue-400">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Sumber</label>
                    <select name="source" class="w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm px-3 py-2 focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200 dark:focus:border-blue-400">
                        <option value="">Semua Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $source)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Tanggal Mulai</label>
                    <x-datepicker name="date_from" value="{{ request('date_from') }}"></x-datepicker>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Tanggal Akhir</label>
                    <x-datepicker name="date_to" value="{{ request('date_to') }}"></x-datepicker>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Filter
                    </button>
                    <a href="{{ route('leads.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 font-medium transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-4 text-center w-10">
                            <input type="checkbox" title="Pilih semua (batch)"
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:checked:bg-blue-600">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Lead / Opportunity</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Partner</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Kebutuhan</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150 group">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" title="Pilih lead ini"
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:checked:bg-blue-600">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $lead->customer->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 dark:text-slate-300">{{ $lead->customer->company ?? $lead->customer->name ?? '-' }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                    @if($lead->pt_group)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold ring-1 ring-inset ring-black/10 {{ \App\Models\Lead::PT_COLORS[$lead->pt_group] ?? 'bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-200' }} mr-1">{{ $lead->pt_group }}</span>@endif
                                    {{ $lead->customer->contact_person ? 'PIC: '.$lead->customer->contact_person : '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
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
                            <td class="px-6 py-4 text-left">
                                @if($lead->source)
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-left">
                                @if($lead->partner)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ \App\Models\Partner::TYPE_DOTS[$lead->partner->type] ?? '#94a3b8' }}"></span>
                                        {{ $lead->partner->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($lead->kebutuhan)
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ Str::limit($lead->kebutuhan, 40) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($lead->incoming_date)
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $lead->incoming_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('view-marketing')
                                        <a href="{{ route('leads.show', $lead) }}" title="Lihat detail lead"
                                           class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-100 dark:text-indigo-400 dark:hover:bg-indigo-500/10 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('manage-marketing')
                                        <a href="{{ route('leads.edit', $lead) }}" title="Edit lead"
                                           class="p-2 rounded-lg text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-500/10 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Hapus lead ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus lead"
                                                    class="p-2 rounded-lg text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-500/10 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                Belum ada lead. <a href="{{ route('leads.create') }}" class="text-blue-600 hover:underline">Tambah lead pertama</a>
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