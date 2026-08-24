<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Daftar Lead / Opportunity</h1>
            <p class="text-slate-500 mt-1">Kelola lead marketing dan opportunity sales</p>
        </div>
        @can('manage-marketing')
            <a href="{{ route('leads.create') }}"
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                + Tambah Lead
            </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600">
        <div class="p-5 border-b dark:border-slate-600 bg-slate-50 dark:bg-slate-700 rounded-t-2xl">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-500">Cari Customer</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama customer..."
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">Status</label>
                    <select name="status" class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">Sumber</label>
                    <select name="source" class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Sumber</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $source)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">Tanggal Mulai</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">Tanggal Akhir</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2 lg:col-span-5 flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Filter
                    </button>
                    <a href="{{ route('leads.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Lead / Opportunity</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Kebutuhan</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-200 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $lead->customer->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $lead->customer->name ?? '-' }}</div>
                                @if($lead->customer->company)
                                    <div class="text-sm text-slate-500">{{ $lead->customer->company }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                    @switch($lead->status)
                                        @case('new') bg-blue-100 text-blue-800 @break
                                        @case('contacted') bg-yellow-100 text-yellow-800 @break
                                        @case('qualified') bg-purple-100 text-purple-800 @break
                                        @case('proposal') bg-orange-100 text-orange-800 @break
                                        @case('won') bg-green-100 text-green-800 @break
                                        @case('lost') bg-red-100 text-red-800 @break
                                        @default bg-slate-100 text-slate-800
                                    @endswitch
                                ">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-left">
                                @if($lead->source)
                                    <span class="text-sm text-slate-700">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($lead->kebutuhan)
                                    <span class="text-sm text-slate-700">{{ Str::limit($lead->kebutuhan, 40) }}</span>
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
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('view-marketing')
                                        <a href="{{ route('leads.show', $lead) }}"
                                           class="px-3 py-1.5 text-sm rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition">
                                            Lihat
                                        </a>
                                    @endcan
                                    @can('manage-marketing')
                                        <a href="{{ route('leads.edit', $lead) }}"
                                           class="px-3 py-1.5 text-sm rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Hapus lead ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-sm rounded-lg bg-red-100 hover:bg-red-200 text-red-700 font-medium transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
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