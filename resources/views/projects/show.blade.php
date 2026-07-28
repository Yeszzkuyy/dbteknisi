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
            <a href="{{ route('customers.show', $project->customer_id) }}" 
               class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div><p class="text-xs text-slate-400">Nama Project</p><p class="font-medium">{{ $project->project_name }}</p></div>
            <div><p class="text-xs text-slate-400">Customer</p><p class="font-medium">{{ $project->customer?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">Jenis Pekerjaan</p><p class="font-medium">{{ $project->workType?->name ?? '-' }}</p></div>
            <div><p class="text-xs text-slate-400">Status</p><span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $project->status ?? 'Open' }}</span></div>
            <div><p class="text-xs text-slate-400">Account Manager</p><p class="font-medium">{{ $project->accountManager?->name ?? '-' }}</p></div>
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
