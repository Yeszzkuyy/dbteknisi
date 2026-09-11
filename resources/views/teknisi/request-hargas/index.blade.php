<x-app-layout>
    @php
        $statusColors = [
            'draft' => 'slate',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'emerald',
        ];
    @endphp

    <div class="px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Request Harga</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Permintaan harga perangkat untuk project</p>
            </div>
            <a href="{{ route('teknisi.request-hargas.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 transition">
                + Buat Request Harga
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('teknisi.request-hargas.index') }}"
              class="flex flex-wrap items-end gap-3 mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="w-full sm:w-56">
                <label class="block text-sm font-medium text-slate-700 mb-1 dark:text-slate-300">Status</label>
                <select name="status"
                        class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100">
                    <option value="">Semua Status</option>
                    @foreach($statusColors as $status => $color)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-64">
                <label class="block text-sm font-medium text-slate-700 mb-1 dark:text-slate-300">Project</label>
                <select name="project_id"
                        class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-slate-100">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-medium transition">
                    Filter
                </button>
                <a href="{{ route('teknisi.request-hargas.index') }}"
                   class="rounded-xl border border-slate-300 text-slate-700 hover:bg-white px-5 py-2.5 text-sm font-medium transition dark:border-slate-600 dark:text-slate-200">
                    Reset
                </a>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Project</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Customer</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Jumlah Item</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Status</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($requestHargas as $requestHarga)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $requestHarga->project?->project_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    {{ $requestHarga->project?->customer?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                    {{ count($requestHarga->items ?? []) }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-status-badge :color="$statusColors[$requestHarga->status] ?? 'slate'">
                                        {{ ucfirst($requestHarga->status) }}
                                    </x-status-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('teknisi.request-hargas.show', $requestHarga) }}"
                                           title="Lihat detail"
                                           class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('teknisi.request-hargas.edit', $requestHarga) }}"
                                           title="Edit"
                                           class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('teknisi.request-hargas.destroy', $requestHarga) }}" method="POST"
                                              onsubmit="return confirm('Hapus request harga ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                    class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-slate-400">
                                    Belum ada request harga.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>