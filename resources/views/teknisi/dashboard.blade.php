<x-app-layout>
<div class="px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Dashboard Teknisi</h1>
            <p class="text-slate-500 mt-1">Ringkasan pekerjaan teknisi hari ini, {{ $now->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('teknisi.jadwal') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
            <x-icon name="calendar" class="w-4 h-4" />
            Buka Jadwal
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-blue-50">
                    <x-icon name="users" class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Teknisi</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $technicians->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-green-50">
                    <x-icon name="activity" class="w-6 h-6 text-green-600" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Teknisi Aktif</p>
                    <p class="text-2xl font-bold text-slate-800">{{ count($activeTechnicians) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-green-50">
                    <x-icon name="check" class="w-6 h-6 text-green-600" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Project Selesai</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $doneProjects }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-indigo-50">
                    <x-icon name="tools" class="w-6 h-6 text-indigo-600" />
                </div>
                <div>
                    <p class="text-sm text-slate-500">Project Berjalan</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $runningProjects->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Pekerjaan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <x-section-header title="Progress Pekerjaan" />

        @php
            $statusCounts = $statusCounts->reject(fn ($s) => $s['name'] === 'Maintenance');
            $totalShown = $statusCounts->sum('count');
            $total = $totalShown > 0 ? $totalShown : 1;
            $segments = '';
            $cursor = 0;
            foreach ($statusCounts as $status) {
                $deg = round($status['count'] / $total * 360);
                if ($deg > 0) {
                    $segments .= ($segments ? ', ' : '') . ($statusBarColors[$status['name']] ?? '#64748b') . ' ' . $cursor . 'deg ' . ($cursor + $deg) . 'deg';
                    $cursor += $deg;
                }
            }
            if ($cursor < 360) {
                $segments .= ($segments ? ', ' : '') . '#e2e8f0 ' . $cursor . 'deg 360deg';
            }
            $doneCount = $statusCounts->firstWhere('name', 'Done')['count'] ?? 0;
            $donePct = $totalShown > 0 ? round($doneCount / $totalShown * 100) : 0;
        @endphp

        <div class="flex flex-col sm:flex-row items-center gap-8">
            <div class="w-48 h-48 rounded-full shrink-0 relative" style="background: conic-gradient({{ $segments }})">
                <div class="absolute inset-6 rounded-full bg-white flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-slate-800 tabular-nums">{{ $donePct }}%</span>
                    <span class="text-xs text-slate-500 mt-1">Selesai</span>
                </div>
            </div>
            <ul class="w-full space-y-2.5">
                @forelse($statusCounts as $status)
                    <li class="flex items-center justify-between gap-4 text-sm">
                        <span class="flex items-center gap-2.5 min-w-0">
                            <span class="w-3 h-3 rounded-full shrink-0"
                                  style="background-color: {{ $statusBarColors[$status['name']] ?? '#64748b' }}"></span>
                            <span class="font-semibold text-slate-700 truncate">{{ $status['name'] }}</span>
                        </span>
                        <span class="font-bold text-slate-800 tabular-nums shrink-0">{{ $status['count'] }} Project</span>
                    </li>
                @empty
                    <li><x-empty-state label="data status project" /></li>
                @endforelse
            </ul>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500">Total Project: <span class="font-bold text-slate-800">{{ $totalShown }}</span></p>
        </div>
    </div>

    {{-- Project Terbaru --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <x-section-header title="Project Terbaru">
            <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                Lihat Semua <x-icon name="chevron-right" class="w-4 h-4" />
            </a>
        </x-section-header>

        @forelse($recentProjects as $project)
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 py-3 border-b border-slate-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $project->project_name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $project->customer?->name ?? '-' }}</p>
                </div>
                <div class="text-xs text-slate-500 shrink-0">
                    Teknisi: {{ $project->pic_engineer ?: ($project->support_technicians ?: '-') }}
                </div>
                <x-status-badge :color="($project->status ? ($statusBadgeColors[$project->status->name] ?? 'slate') : 'slate')">
                    {{ $project->status?->name ?? '-' }}
                </x-status-badge>
                <div class="text-xs text-slate-400 shrink-0 w-24 text-right">
                    {{ $project->created_at ? $project->created_at->setTimezone('Asia/Jakarta')->format('d M Y') : '-' }}
                </div>
            </div>
        @empty
            <x-empty-state label="project" />
        @endforelse
    </div>

    {{-- Aktivitas Terbaru + Teknisi Aktif --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Aktivitas Terbaru --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <x-section-header title="Aktivitas Terbaru" />

            @forelse($activities as $activity)
                <div class="flex items-start gap-4 py-3 border-b border-slate-100 last:border-0">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span class="text-indigo-600 font-semibold text-sm">{{ strtoupper(substr($activity->user?->name ?? 'S', 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800">
                            <span class="font-semibold">{{ $activity->user?->name ?? 'System' }}</span>
                            {{ $activity->title ?? 'Aktivitas' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            @if($activity->project)
                                {{ $activity->project->project_name }} ·
                            @endif
                            {{ $activity->activity_date?->setTimezone('Asia/Jakarta')->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <x-empty-state label="aktivitas" />
            @endforelse
        </div>

        {{-- Teknisi Aktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <x-section-header title="Teknisi Aktif" />

            @forelse($activeTechnicians as $technician)
                <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-semibold text-sm">{{ strtoupper(substr($technician->name, 0, 1)) }}</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-500 border-2 border-white"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $technician->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $currentProjectByTechnician[$technician->id]->project_name }}</p>
                    </div>
                    <x-status-badge color="green">Aktif</x-status-badge>
                </div>
            @empty
                <x-empty-state label="teknisi aktif" description="Teknisi dianggap aktif saat memiliki project yang sedang berjalan." />
            @endforelse
        </div>
    </div>
</div>
</x-app-layout>