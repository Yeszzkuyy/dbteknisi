<x-app-layout>
    {{-- Sapaan --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mt-1">
                Selamat datang kembali, {{ auth()->user()->name }}
            </h1>
            <p class="text-slate-500 mt-1">Ini ringkasan hari ini di 3DY Group.</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8" data-reveal>
        {{-- Total Customer --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-blue-500/5 group-hover:bg-blue-500/10 transition-colors duration-500"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total Customer</p>
                    <p class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2"
                       x-data="counter({{ $customerCount ?? 0 }})" x-init="start()" x-text="display">{{ $customerCount ?? 0 }}</p>
                </div>
                <div class="shrink-0 p-3 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Project --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-violet-500/5 group-hover:bg-violet-500/10 transition-colors duration-500"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total Project</p>
                    <p class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2"
                       x-data="counter({{ $totalProjects ?? 0 }})" x-init="start()" x-text="display">{{ $totalProjects ?? 0 }}</p>
                </div>
                <div class="shrink-0 p-3 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Project Aktif --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-emerald-500/5 group-hover:bg-emerald-500/10 transition-colors duration-500"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Project Aktif</p>
                    <p class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2"
                       x-data="counter({{ $activeProjects ?? 0 }})" x-init="start()" x-text="display">{{ $activeProjects ?? 0 }}</p>
                </div>
                <div class="shrink-0 p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Dokumen --}}
        <div class="group relative overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-amber-500/5 group-hover:bg-amber-500/10 transition-colors duration-500"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total Dokumen</p>
                    <p class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2"
                       x-data="counter({{ $documentCount ?? 0 }})" x-init="start()" x-text="display">{{ $documentCount ?? 0 }}</p>
                </div>
                <div class="shrink-0 p-3 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8" data-reveal data-reveal-delay="1">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900">Aktivitas Terbaru</h3>
        </div>

        @forelse($activities as $activity)
            <div class="relative flex items-start gap-4 pb-6 last:pb-0">
                @if(! $loop->last)
                    <span class="absolute left-[19px] top-11 bottom-0 w-px bg-slate-100"></span>
                @endif
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-bold shadow-md shadow-indigo-500/20">
                    {{ strtoupper(substr($activity->user?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <p class="text-sm text-slate-800 leading-snug">
                        <span class="font-bold">{{ $activity->user?->name ?? 'System' }}</span>
                        {{ $activity->title ?? 'Activity' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $activity->project?->name ?? 'Project' }} · {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm mt-4">Belum ada aktivitas — mulai dari membuat project pertama.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
