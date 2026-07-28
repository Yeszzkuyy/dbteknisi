<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Daftar Project Aktif</h1>
            <p class="text-slate-500 mt-1">Project dengan status Open atau Progress</p>
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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">Project</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">Customer</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">Jenis Pekerjaan</th>
                        {{-- <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">PIC Engineer</th> --}}
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-wider text-slate-500">Progress</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $project->project_name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->customer?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $project->workType?->name ?? '-' }}</td>
                            {{-- <td class="px-6 py-4 text-slate-600">{{ $project->pic_engineer ?? '-' }}</td> --}}
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($project->status == 'Open')
                                        bg-blue-100 text-blue-700
                                    @elseif($project->status == 'Progress')
                                        bg-yellow-100 text-yellow-700
                                    @endif
                                ">
                                    {{ $project->status ?? 'Open' }}
                                </span>
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
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('customers.show', $project->customer_id) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 text-sm">Lihat Customer</a>
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                    @can('manage-teknisi')
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="text-amber-600 hover:text-amber-800 text-sm">Edit</a>
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