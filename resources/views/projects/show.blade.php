<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">{{ $project->project_name }}</h1>
            <p class="text-slate-500 mt-1">Detail Project</p>
        </div>
        <div class="flex gap-2">
            @can('manage-teknisi')
                <a href="{{ route('projects.edit', $project) }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-medium transition">Edit Project</a>
            @endcan
            <a href="{{ route('projects.index') }}"
               class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-medium transition">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div><p class="text-xs text-slate-400">Nama Project</p><p class="font-medium text-slate-800">{{ $project->project_name }}</p></div>
            <div><p class="text-xs text-slate-400">Customer</p><p class="font-medium text-slate-800">{{ $project->customer?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">Jenis Pekerjaan</p><p class="font-medium text-slate-800">{{ $project->workType?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">Status</p><x-status-badge :color="$project->status?->color ?? 'slate'">{{ $project->status?->name ?? 'Belum Memulai' }}</x-status-badge></div>
            <div><p class="text-xs text-slate-400">Account Manager</p><p class="font-medium text-slate-800">{{ $project->accountManager?->name ?? '-' }}</p></div>
            <div>
                <p class="text-xs text-slate-400">PIC Engineer</p>
                <p class="font-medium text-slate-800">{{ $project->pic_engineer ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Support Technicians</p>
                <p class="font-medium text-slate-800">{{ $project->support_technicians ?? '-' }}</p>
            </div>
        </div>
        @if($project->description)
            <div class="mt-4 pt-4 border-t"><p class="text-xs text-slate-400">Deskripsi</p><p class="text-slate-700">{{ $project->description }}</p></div>
        @endif
    </div>
</div>
</x-app-layout>
