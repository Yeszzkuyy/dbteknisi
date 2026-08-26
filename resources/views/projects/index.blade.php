<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Daftar Project</h1>
            <p class="text-slate-500 mt-1">Semua project, termasuk yang selesai atau di-hold</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition">
                Lihat Customer
            </a>
            @can('manage-teknisi')
                <a href="{{ route('projects.create') }}" 
                   class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    + Tambah Project
                </a>
            @endcan
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Total Project</p>
            <p class="text-2xl font-bold text-slate-800">{{ $totalProjects ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Project Aktif (Open + Progress)</p>
            <p class="text-2xl font-bold text-green-600">{{ $activeProjects ?? 0 }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Project</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Customer</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Jenis Pekerjaan</th>
                        {{-- <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">PIC Engineer</th> --}}
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Progress</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $project->project_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->customer?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->workType?->name ?? '-' }}</td>
                            {{-- <td class="px-6 py-4 text-slate-600">{{ $project->pic_engineer ?? '-' }}</td> --}}
                            <td class="px-6 py-4">
                                <x-status-badge :color="$project->status?->color ?? 'slate'">
                                    {{ $project->status?->name ?? 'Belum Memulai' }}
                                </x-status-badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-600">{{ $project->progress ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('customers.show', $project->customer_id) }}"
                                       title="Lihat customer"
                                       class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('projects.show', $project) }}"
                                       title="Detail project"
                                       class="p-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    @can('manage-teknisi')
                                        <a href="{{ route('projects.edit', $project) }}"
                                           title="Edit project"
                                           class="p-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                Belum ada project yang aktif (Open / Progress).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>