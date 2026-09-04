<x-app-layout>
    @php
        $dashboardCards = [
            [
                'label' => 'Total Customer',
                'value' => $customerCount ?? 0,
                'description' => 'Customer terdaftar',
                'icon' => 'users',
                'iconClass' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300',
                'washClass' => 'bg-blue-500/5 dark:bg-blue-400/10',
            ],
            [
                'label' => 'Total Project',
                'value' => $totalProjects ?? 0,
                'description' => 'Seluruh project',
                'icon' => 'folder',
                'iconClass' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300',
                'washClass' => 'bg-indigo-500/5 dark:bg-indigo-400/10',
            ],
            [
                'label' => 'Project Aktif',
                'value' => $activeProjects ?? 0,
                'description' => 'Open dan On Progress',
                'icon' => 'activity',
                'iconClass' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                'washClass' => 'bg-emerald-500/5 dark:bg-emerald-400/10',
            ],
            [
                'label' => 'Total Dokumen',
                'value' => $documentCount ?? 0,
                'description' => 'Dokumen project',
                'icon' => 'book',
                'iconClass' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                'washClass' => 'bg-amber-500/5 dark:bg-amber-400/10',
            ],
        ];
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
                            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                        </p>
                        <span class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block dark:bg-slate-600"></span>
                        <span class="text-xs font-medium text-slate-400">Ringkasan operasional</span>
                    </div>
                    <h1 class="mt-3 max-w-3xl text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl dark:text-slate-100">
                        Selamat datang kembali, {{ auth()->user()->name }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        Pantau kondisi customer, project, dan dokumentasi 3DY Group dari satu tempat.
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm shadow-blue-600/20">
                        <x-icon name="grid" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dashboard umum</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-700 dark:text-slate-200">Overview 3DY Group</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Summary cards --}}
        <section aria-labelledby="dashboard-summary-heading" data-reveal data-reveal-delay="1">
            <div class="mb-3 flex flex-wrap items-end justify-between gap-2 px-1">
                <div>
                    <h2 id="dashboard-summary-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Ringkasan utama</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Angka terbaru dari data operasional yang tersedia.</p>
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

        {{-- Recent activities --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 dark:border-slate-700 dark:bg-slate-800" data-reveal data-reveal-delay="2" aria-labelledby="recent-activities-heading">
            <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-700">
                <div>
                    <h2 id="recent-activities-heading" class="text-lg font-bold text-slate-900 dark:text-slate-100">Aktivitas terbaru</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Perubahan terakhir yang tercatat pada project.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-700 dark:text-slate-300">
                    {{ $activities->count() }} aktivitas
                </span>
            </div>

            @forelse($activities as $activity)
                <article class="group flex items-start gap-4 border-b border-slate-100 py-4 last:border-0 last:pb-0 dark:border-slate-700">
                    <div class="relative shrink-0">
                        <x-user-avatar :user="$activity->user" size="w-10 h-10" text="text-sm" />
                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-blue-500 dark:border-slate-800"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between sm:gap-4">
                            <p class="min-w-0 text-sm leading-snug text-slate-700 dark:text-slate-200">
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ $activity->user?->name ?? 'System' }}</span>
                                <span class="text-slate-600 dark:text-slate-300">{{ $activity->title ?? 'Aktivitas' }}</span>
                            </p>
                            <time class="shrink-0 text-xs font-medium text-slate-400" datetime="{{ $activity->activity_date?->toIso8601String() }}">
                                {{ $activity->activity_date?->setTimezone('Asia/Jakarta')->diffForHumans() ?? 'Waktu tidak tersedia' }}
                            </time>
                        </div>
                        <p class="mt-1 truncate text-xs text-slate-400 dark:text-slate-500">
                            {{ $activity->project?->project_name ?? 'Project tidak tersedia' }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl bg-slate-50 px-5 py-12 text-center dark:bg-slate-900/40">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-slate-300 shadow-sm dark:bg-slate-800 dark:text-slate-500">
                        <x-icon name="activity" class="h-6 w-6" />
                    </span>
                    <p class="mt-4 text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada aktivitas yang tercatat.</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Aktivitas project akan muncul di sini.</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>
