<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Daftar Instalasi</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola jadwal dan progress instalasi</p>
        </div>
        <a href="{{ route('teknisi.instalasis.create') }}"
           class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
            + Tambah Instalasi
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-4 mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('teknisi.instalasis.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Status</label>
                <select name="status" class="rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua</option>
                    @foreach(['scheduled' => 'Scheduled', 'on_progress' => 'On Progress', 'waiting' => 'Waiting', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $lbl)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Project</label>
                <select name="project_id" class="rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }} — {{ $project->customer?->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">Filter</button>
            @if(request()->hasAny(['status', 'project_id']))
                <a href="{{ route('teknisi.instalasis.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Project</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Customer</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">PIC Teknisi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Jadwal</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Status</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($instalasis as $instalasi)
                        @php
                            $statusColors = [
                                'scheduled' => 'blue',
                                'on_progress' => 'yellow',
                                'waiting' => 'orange',
                                'completed' => 'green',
                                'cancelled' => 'slate',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-100">
                                {{ $instalasi->project?->project_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ $instalasi->project?->customer?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $instalasi->location ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $instalasi->technician_pic ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ $instalasi->schedule_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :color="$statusColors[$instalasi->status] ?? 'slate'">
                                    {{ str($instalasi->status)->replace('_', ' ')->title() }}
                                </x-status-badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('teknisi.instalasis.show', $instalasi) }}"
                                       title="Detail"
                                       class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('teknisi.instalasis.edit', $instalasi) }}"
                                       title="Edit"
                                       class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('teknisi.instalasis.destroy', $instalasi) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus instalasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="p-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
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
                            <td colspan="7" class="py-16 text-center text-slate-400 dark:text-slate-500">
                                Belum ada data instalasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
