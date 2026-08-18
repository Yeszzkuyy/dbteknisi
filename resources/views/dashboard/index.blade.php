<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 mt-1">
                Selamat datang kembali, {{ auth()->user()->name }}
            </p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        
        {{-- Total Customer --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-blue-50">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Customer</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $customerCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    
        {{-- Total Project --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-indigo-50">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Project</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalProjects ?? 0 }}</p>
                </div>
            </div>
        </div>
    
        {{-- Project Aktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-green-50">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Project Aktif</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $activeProjects ?? 0 }}</p>
                </div>
            </div>
        </div>
    
        {{-- Total Dokumen --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-purple-50">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Dokumen</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $documentCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    
    </div>

    {{-- Recent Activities --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Aktivitas Terbaru</h3>
        
        @forelse($activities as $activity)
            <div class="flex items-start gap-4 py-3 border-b border-slate-100 last:border-0">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-indigo-600 font-semibold text-sm">
                        {{ strtoupper(substr($activity->user?->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-slate-800">
                        <span class="font-semibold">{{ $activity->user?->name ?? 'System' }}</span>
                        {{ $activity->title ?? 'Activity' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $activity->project?->name ?? 'Project' }} · 
                        {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-slate-400 text-center py-8">Belum ada aktivitas.</p>
        @endforelse
    </div>
</x-app-layout>