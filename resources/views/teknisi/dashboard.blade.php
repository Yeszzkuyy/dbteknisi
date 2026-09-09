<x-app-layout>
    @php
        $dashboardCards = [
            [
                'label' => 'Total Teknisi',
                'value' => $technicians->count(),
                'description' => 'Seluruh teknisi terdaftar',
                'icon' => 'users',
                'iconClass' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300',
                'washClass' => 'bg-blue-500/5 dark:bg-blue-400/10',
            ],
            [
                'label' => 'Teknisi Aktif',
                'value' => count($activeTechnicians),
                'description' => 'Punya project berjalan',
                'icon' => 'activity',
                'iconClass' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                'washClass' => 'bg-emerald-500/5 dark:bg-emerald-400/10',
            ],
            [
                'label' => 'Project Selesai',
                'value' => $doneProjects,
                'description' => 'Status project Done',
                'icon' => 'check-circle',
                'iconClass' => 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-300',
                'washClass' => 'bg-green-500/5 dark:bg-green-400/10',
            ],
            [
                'label' => 'Project Berjalan',
                'value' => $runningProjects->count(),
                'description' => 'Open dan On Progress',
                'icon' => 'tools',
                'iconClass' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300',
                'washClass' => 'bg-indigo-500/5 dark:bg-indigo-400/10',
            ],
        ];
    @endphp

    @php
        // Donut: maksimal 6 irisan. Top 5 status terbesar tampil sendiri,
        // sisanya digabung "Lainnya" agar warna + proporsi tetap terbaca.
        $donutStatuses = $statusCounts
            ->reject(fn ($s) => $s['name'] === 'Maintenance')
            ->filter(fn ($s) => $s['count'] > 0)
            ->sortByDesc('count')
            ->values();
        $topStatuses = $donutStatuses->take(5);
        $othersCount = $donutStatuses->skip(5)->sum('count');
        $totalShown = $donutStatuses->sum('count');

        $donutData = $topStatuses->map(fn ($s) => [
            'label' => $s['name'],
            'value' => $s['count'],
            'color' => $statusBarColors[$s['name']] ?? '#64748b',
        ])->values();
        if ($othersCount > 0) {
            $donutData->push(['label' => 'Lainnya', 'value' => $othersCount, 'color' => '#64748b']);
        }

        $doneCount = $statusCounts->firstWhere('name', 'Done')['count'] ?? 0;
        $donePct = $totalShown > 0 ? round($doneCount / $totalShown * 100) : 0;
        $pct = fn ($n) => $totalShown > 0 ? round($n / $totalShown * 100) : 0;
    @endphp

    <div class="max-w-[1400px] mx-auto space-y-6">
        {{-- Overview header --}}
        <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 py-6 shadow-sm sm:px-7 sm:py-7 dark:border-slate-700 dark:bg-slate-800" data-reveal>
            <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-blue-500/5 dark:bg-blue-400/10"></div>
            <div class="pointer-events-none absolute bottom-0 right-24 h-1 w-28 rounded-full bg-blue-500/30"></div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600 dark:text-blue-300">
                            {{ $now->translatedFormat('l, d F Y') }}
                        </p>
                        <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block dark:bg-slate-600"></span>
                        <span class="text-xs font-medium text-slate-400">Ringkasan pekerjaan teknisi</span>
                    </div>
                    <h1 class="mt-3 max-w-3xl text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl dark:text-slate-100">
                        Selamat datang kembali, {{ auth()->user()->name }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        Pantau status project, aktivitas, dan kesibukan teknisi di divisi teknis.
                    </p>
                </div>

                <div class="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm shadow-blue-600/20">
                            <x-icon name="tools" class="h-5 w-5" />
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dashboard teknisi</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-700 dark:text-slate-200">Overview Divisi Teknis</p>
                        </div>
                    </div>
                    <a href="{{ route('teknisi.jadwal') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-700">
                        <x-icon name="calendar" class="h-4 w-4" />
                        Buka Jadwal
                    </a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Summary cards (full width row) --}}
            <section class="lg:col-span-12" aria-labelledby="dashboard-summary-heading" data-reveal data-reveal-delay="1">
                <div class="mb-3 flex flex-wrap items-end justify-between gap-2 px-1">
                    <div>
                        <h2 id="dashboard-summary-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Ringkasan utama</h2>
                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Angka terbaru dari data operasional teknisi.</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">{{ count($dashboardCards) }} indikator</span>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($dashboardCards as $card)
                        <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:border-blue-200 sm:p-6 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-blue-500/40">
                            <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full {{ $card['washClass'] }} transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ $card['label'] }}</p>
                                    <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums dark:text-slate-100"
                                       x-data="counter({{ $card['value'] }})" x-init="start()" x-text="display">{{ $card['value'] }}</p>
                                    <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">{{ $card['description'] }}</p>
                                </div>
                                <span class="shrink-0 rounded-xl p-3 {{ $card['iconClass'] }}">
                                    <x-icon name="{{ $card['icon'] }}" class="h-5 w-5" />
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Progress Pekerjaan (bento: donut + legenda berpersentase) --}}
            <section class="lg:col-span-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-800" data-reveal data-reveal-delay="2" aria-labelledby="progress-heading">
                <div class="border-b border-slate-100 pb-4 dark:border-slate-700">
                    <h2 id="progress-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Progress Pekerjaan</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Distribusi project berdasarkan status.</p>
                </div>

                <div class="mt-6 flex flex-col items-center gap-6">
                    {{-- Donut progress (ApexCharts) --}}
                    <div class="relative w-full max-w-[260px]">
                        <div id="teknisi-donut-chart" class="w-full"></div>
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Selesai</span>
                            <span class="text-3xl font-bold text-slate-800 tabular-nums dark:text-slate-100"
                                  x-data="counter({{ $donePct }})" x-init="start()" x-text="display + '%'">0%</span>
                        </div>
                    </div>

                    <ul class="w-full space-y-1.5">
                        @forelse($topStatuses as $status)
                            <li class="flex items-center justify-between gap-4 rounded-lg px-2 py-1.5 text-sm transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                <span class="flex min-w-0 items-center gap-2.5">
                                    <span class="h-3 w-3 shrink-0 rounded-full"
                                          style="background-color: {{ $statusBarColors[$status['name']] ?? '#64748b' }}"></span>
                                    <span class="truncate font-semibold text-slate-700 dark:text-slate-200">{{ $status['name'] }}</span>
                                </span>
                                <span class="shrink-0 font-bold text-slate-800 tabular-nums dark:text-slate-100">
                                    {{ $status['count'] }} Project
                                    <span class="font-medium text-slate-400">· {{ $pct($status['count']) }}%</span>
                                </span>
                            </li>
                        @empty
                            <li><x-empty-state label="data status project" /></li>
                        @endforelse

                        @if($othersCount > 0)
                            <li class="flex items-center justify-between gap-4 rounded-lg px-2 py-1.5 text-sm transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                <span class="flex min-w-0 items-center gap-2.5">
                                    <span class="h-3 w-3 shrink-0 rounded-full bg-slate-400"></span>
                                    <span class="truncate font-semibold text-slate-700 dark:text-slate-200">Lainnya</span>
                                </span>
                                <span class="shrink-0 font-bold text-slate-800 tabular-nums dark:text-slate-100">
                                    {{ $othersCount }} Project
                                    <span class="font-medium text-slate-400">· {{ $pct($othersCount) }}%</span>
                                </span>
                            </li>
                        @endif
                    </ul>

                    <div class="w-full border-t border-slate-100 pt-4 dark:border-slate-700">
                        <p class="text-sm text-slate-500">Total Project: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $totalShown }}</span></p>
                    </div>
                </div>
            </section>

            {{-- Project Terbaru (bento: kolom lebar) --}}
            <section class="lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-800" data-reveal data-reveal-delay="3" aria-labelledby="recent-projects-heading">
                <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-700">
                    <div>
                        <h2 id="recent-projects-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Project Terbaru</h2>
                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Project yang baru ditambahkan.</p>
                    </div>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 transition hover:text-blue-700">
                        Lihat Semua <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>

                @forelse($recentProjects as $project)
                    <article class="group flex flex-col gap-2 border-b border-slate-100 py-3 transition duration-200 hover:bg-slate-50/70 last:border-0 sm:flex-row sm:items-center dark:border-slate-700 dark:hover:bg-slate-700/30">
                        <div class="min-w-0 flex-1 px-2">
                            <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $project->project_name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $project->customer?->name ?? '-' }}</p>
                        </div>
                        <div class="shrink-0 px-2 text-xs text-slate-500">
                            Teknisi: {{ $project->pic_engineer ?: ($project->support_technicians ?: '-') }}
                        </div>
                        <x-status-badge :color="($project->status ? ($statusBadgeColors[$project->status->name] ?? 'slate') : 'slate')">
                            {{ $project->status?->name ?? '-' }}
                        </x-status-badge>
                        <div class="w-24 shrink-0 px-2 text-right text-xs text-slate-400">
                            {{ $project->created_at ? $project->created_at->setTimezone('Asia/Jakarta')->format('d M Y') : '-' }}
                        </div>
                    </article>
                @empty
                    <x-empty-state label="project" />
                @endforelse
            </section>

            {{-- Teknisi Aktif (bento: kolom lebar) --}}
            <section class="lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-800" data-reveal data-reveal-delay="4" aria-labelledby="active-technicians-heading">
                <div class="border-b border-slate-100 pb-4 dark:border-slate-700">
                    <h2 id="active-technicians-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Teknisi Aktif</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Teknisi yang sedang menangani project berjalan.</p>
                </div>

                <div class="mt-2">
                    @forelse($activeTechnicians as $technician)
                        <article class="flex items-start gap-3 rounded-xl px-2 py-3 transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                            <div class="relative mt-0.5 shrink-0">
                                <x-user-avatar :user="$technician" size="w-10 h-10" text="text-sm" />
                                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500 dark:border-slate-800"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $technician->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $projectsByTechnician[$technician->id]->count() }} project berjalan</p>
                                <div class="mt-1 flex flex-col gap-0.5">
                                    @foreach($projectsByTechnician[$technician->id] as $project)
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="truncate text-xs font-medium text-blue-600 transition hover:text-blue-700 hover:underline">
                                            {{ $project->project_name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <x-status-badge color="green">Aktif</x-status-badge>
                        </article>
                    @empty
                        <x-empty-state label="teknisi aktif" description="Teknisi dianggap aktif saat memiliki project yang sedang berjalan." />
                    @endforelse

                    @if($idleTechnicians->isNotEmpty() && $activeTechnicians->isNotEmpty())
                        <p class="px-2 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Tidak Aktif</p>
                    @endif

                    @forelse($idleTechnicians as $technician)
                        <article class="flex items-center gap-3 rounded-xl px-2 py-3 opacity-60 transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                            <x-user-avatar :user="$technician" size="w-10 h-10" text="text-sm" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $technician->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">Tidak ada project berjalan</p>
                            </div>
                            <x-status-badge color="slate">Idle</x-status-badge>
                        </article>
                    @empty
                    @endforelse
                </div>
            </section>

            {{-- Aktivitas Terbaru (bento: kolom sempit) --}}
            <section class="lg:col-span-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-800" data-reveal data-reveal-delay="5" aria-labelledby="recent-activities-heading">
                <div class="border-b border-slate-100 pb-4 dark:border-slate-700">
                    <h2 id="recent-activities-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Aktivitas Terbaru</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Perubahan terakhir pada project.</p>
                </div>

                <div class="mt-2">
                    @forelse($activities as $activity)
                        <article class="flex items-start gap-4 rounded-xl px-2 py-3.5 transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-700/40">
                            <x-user-avatar :user="$activity->user" size="w-10 h-10" text="text-sm" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm leading-snug text-slate-700 dark:text-slate-200">
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $activity->user?->name ?? 'System' }}</span>
                                    {{ $activity->title ?? 'Aktivitas' }}
                                </p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    @if($activity->project)
                                        {{ $activity->project->project_name }} ·
                                    @endif
                                    {{ $activity->activity_date?->setTimezone('Asia/Jakarta')->diffForHumans() }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <x-empty-state label="aktivitas" />
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const donutData = @json($donutData);
            if (!donutData.length || typeof window.ApexCharts === 'undefined') return;

            const isDark = document.documentElement.classList.contains('dark');

            new ApexCharts(document.querySelector('#teknisi-donut-chart'), {
                chart: {
                    type: 'donut',
                    height: 240,
                    width: '100%',
                    toolbar: { show: false },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeout',
                        speed: 700,
                    },
                },
                series: donutData.map(d => d.value),
                labels: donutData.map(d => d.label),
                colors: donutData.map(d => d.color),
                theme: { mode: isDark ? 'dark' : 'light' },
                stroke: { width: 3, colors: [isDark ? '#1e293b' : '#ffffff'] },
                fill: { type: 'solid' },
                dataLabels: { enabled: false },
                legend: { show: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                        },
                    },
                },
                tooltip: {
                    enabled: true,
                    theme: isDark ? 'dark' : 'light',
                    y: { formatter: (val) => val + ' project' },
                },
            }).render();
        });
    </script>
</x-app-layout>