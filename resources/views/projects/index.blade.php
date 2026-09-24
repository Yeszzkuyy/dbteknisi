<x-app-layout>
<div class="max-w-[1400px] mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Daftar Project</h1>
            <p class="text-slate-500 mt-1">Semua project, termasuk yang selesai atau di-hold</p>
        </div>
        <div class="flex gap-2">
            @can('manage-teknisi')
                <x-icon-button as="a" icon="add" href="{{ route('projects.create') }}" title="{{ __('Tambah Project') }}" />
            @endcan
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 sm:p-6">
            <p class="text-sm text-slate-500">Total Project</p>
            <p class="mt-2 text-3xl font-bold text-slate-800 tabular-nums">{{ $totalProjects ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 sm:p-6">
            <p class="text-sm text-slate-500">Project Aktif (Open + Progress)</p>
            <p class="mt-2 text-3xl font-bold text-green-600 tabular-nums">{{ $activeProjects ?? 0 }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Project</th>
                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Customer</th>
                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Jenis Pekerjaan</th>
                        {{-- <th class="px-4 py-4 text-left text-xs uppercase tracking-wider text-slate-500">PIC Engineer</th> --}}
                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Progress</th>
                        <th class="px-4 py-3.5 text-right text-xs uppercase tracking-wider text-slate-500 dark:text-slate-200 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-600">
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                            <td class="px-4 py-5 font-semibold text-slate-800 max-w-[220px] truncate">{{ $project->project_name }}</td>
                            <td class="px-4 py-5 text-slate-600 max-w-[180px] truncate">{{ $project->customer?->name ?? '-' }}</td>
                            <td class="px-4 py-5 text-slate-600 max-w-[160px] truncate">{{ $project->workType?->name ?? '-' }}</td>
                            {{-- <td class="px-4 py-4 text-slate-600">{{ $project->pic_engineer ?? '-' }}</td> --}}
                            <td class="px-4 py-5 whitespace-nowrap">
                                <x-status-badge :color="$statusBadgeColors[$project->status?->name] ?? 'slate'">
                                    {{ $project->status?->name ?? 'Belum Memulai' }}
                                </x-status-badge>
                            </td>
                            <td class="px-4 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-20 h-2 bg-slate-200 dark:bg-slate-600 rounded-full overflow-hidden">
                                        <div class="h-full bg-accent-600 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600 tabular-nums">{{ $project->progress ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-5 whitespace-nowrap">
                                <div class="flex justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('customers.show', $project->customer_id) }}"
                                       title="{{ __('Lihat customer') }}"
                                       aria-label="{{ __('Lihat customer') }}"
                                       class="group/btn relative overflow-hidden p-2 rounded-lg bg-accent-50 hover:bg-accent-100 text-accent-700 transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 transition-transform duration-300 group-hover/btn:scale-110" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v16M15 9.5h3a2 2 0 0 1 2 2V21M8 7h3M8 11h3M8 15h3M17 13h1M17 17h1M2.5 21h19" /></svg>
                                        <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover/btn:translate-x-full" aria-hidden="true"></span>
                                    </a>
                                    <a href="{{ route('projects.show', $project) }}"
                                       title="{{ __('Detail project') }}"
                                       aria-label="{{ __('Detail project') }}"
                                       class="group/btn relative overflow-hidden p-2 rounded-lg bg-accent-50 hover:bg-accent-100 text-accent-700 transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95">
                                        <img src="{{ asset('icons/lead-view.svg') }}" alt="" loading="lazy"
                                             class="block h-5 w-5 group-hover/btn:hidden" />
                                        <img src="{{ asset('icons/lead-view.gif') }}" alt="" loading="lazy"
                                             class="hidden h-5 w-5 group-hover/btn:block" />
                                        <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover/btn:translate-x-full" aria-hidden="true"></span>
                                    </a>
                                    @can('manage-teknisi')
                                        <a href="{{ route('projects.edit', $project) }}"
                                           title="{{ __('Edit project') }}"
                                           aria-label="{{ __('Edit project') }}"
                                           class="group/btn relative overflow-hidden p-2 rounded-lg bg-accent-100 hover:bg-accent-200 text-accent-700 transition-all duration-300 hover:scale-110 hover:shadow-md active:scale-95">
                                            <img src="{{ asset('icons/lead-edit.svg') }}" alt="" loading="lazy"
                                                 class="block h-5 w-5 group-hover/btn:hidden" />
                                            <img src="{{ asset('icons/lead-edit.gif') }}" alt="" loading="lazy"
                                                 class="hidden h-5 w-5 group-hover/btn:block" />
                                            <span class="pointer-events-none absolute inset-0 -translate-x-full -skew-x-12 bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover/btn:translate-x-full" aria-hidden="true"></span>
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