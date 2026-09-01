<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Dashboard Marketing</h1>
            <p class="text-slate-500 mt-1">Ringkasan performa lead dan pipeline</p>
        </div>
        <a href="{{ route('leads.index') }}"
           class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition">
            Lihat Lead
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5 mb-6">
        <form method="GET" action="{{ route('marketing.dashboard') }}" class="flex flex-wrap items-end gap-x-6 gap-y-4">
            <div class="flex flex-wrap sm:flex-nowrap items-end gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-500">Dari Tanggal</label>
                    <x-datepicker name="date_from" value="{{ $dateFrom }}" class="mt-1"></x-datepicker>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-500">Sampai Tanggal</label>
                    <x-datepicker name="date_to" value="{{ $dateTo }}" class="mt-1"></x-datepicker>
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                    Filter
                </button>
                <a href="{{ route('marketing.dashboard') }}"
                   class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700 font-medium transition">
                    Reset
                </a>
            </div>

            <div class="flex items-end">
                @php
                    $presets = [
                        'Bulan Ini' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
                        '3 Bulan' => [now()->subMonths(2)->startOfMonth()->toDateString(), now()->toDateString()],
                        '6 Bulan' => [now()->subMonths(5)->startOfMonth()->toDateString(), now()->toDateString()],
                    ];
                    $activeRange = $dateFrom . '|' . $dateTo;
                @endphp
                <div class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-600 overflow-hidden divide-x divide-slate-200 dark:divide-slate-600">
                    @foreach($presets as $label => [$from, $to])
                        @php($active = $activeRange === $from . '|' . $to)
                        <a href="{{ route('marketing.dashboard', ['date_from' => $from, 'date_to' => $to]) }}"
                           class="px-4 py-2.5 text-sm font-medium transition {{ $active ? 'bg-blue-600 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="relative h-full flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-blue-500/5"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Lead Bulan Ini</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ $stats['this_month'] }}</p>
                    <p class="text-xs mt-1 {{ $stats['this_month'] >= $stats['last_month'] ? 'text-green-600' : 'text-red-500' }}">
                        {{ $stats['this_month'] >= $stats['last_month'] ? '▲' : '▼' }} bulan lalu: {{ $stats['last_month'] }}
                    </p>
                </div>
                <div class="shrink-0 p-2.5 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>

        <div class="relative h-full flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-indigo-500/5"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Lead</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ $stats['total'] }}</p>
                </div>
                <div class="shrink-0 p-2.5 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="relative h-full flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-amber-500/5"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Lead Aktif</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ $stats['active'] }}</p>
                </div>
                <div class="shrink-0 p-2.5 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>

        <div class="relative h-full flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-green-500/5"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Won</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ $stats['won'] }}</p>
                    <p class="text-xs text-red-500 mt-1">Lost: {{ $stats['lost'] }}</p>
                </div>
                <div class="shrink-0 p-2.5 rounded-xl bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="relative h-full flex flex-col justify-between overflow-hidden bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-cyan-500/5"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Conversion Rate</p>
                    <p class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ $stats['conversion'] }}%</p>
                    <p class="text-xs text-slate-400 mt-1">won ÷ (won + lost)</p>
                </div>
                <div class="shrink-0 p-2.5 rounded-xl bg-cyan-100 text-cyan-600 dark:bg-cyan-900/40 dark:text-cyan-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Tren lead masuk --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
            <h2 class="font-semibold text-slate-700 mb-4">Lead Masuk — {{ $dateFrom }} s/d {{ $dateTo }}</h2>
            @php($maxTrend = max($trend->max('total'), 1))
            <div class="flex items-end justify-between gap-3 h-44">
                @foreach($trend as $month)
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <span class="text-xs font-semibold text-slate-600">{{ $month->total }}</span>
                        <div class="w-full max-w-12 rounded-t-[4px] bg-blue-500 transition-all"
                             style="height: {{ max(round($month->total / $maxTrend * 100), 2) }}%"></div>
                        <span class="text-[11px] text-slate-500 whitespace-nowrap">{{ $month->label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Lead per sumber --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
            <h2 class="font-semibold text-slate-700 mb-4">Lead per Sumber</h2>
            @if($perSource->isEmpty())
                <p class="text-sm text-slate-500 py-8 text-center">Belum ada data lead.</p>
            @else
                @php($maxSource = max($perSource->max('total'), 1))
                <div class="space-y-3">
                    @foreach($perSource as $row)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-600 capitalize">{{ str_replace('_', ' ', $row->source) }}</span>
                                <span class="font-semibold text-slate-700">{{ $row->total }}</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-500"
                                     style="width: {{ round($row->total / $maxSource * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Donut Lead per Status (ApexCharts) --}}
    <div class="w-full bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 mt-4">
        <h2 class="font-semibold text-slate-700 mb-4">Pipeline Lead per Status</h2>
        <div id="status-donut-chart" class="w-full"></div>
        <script>
            // Tunggu DOM siap: bundle Vite dimuat sebagai module (deferred),
            // jadi window.ApexCharts baru tersedia setelah DOMContentLoaded.
            document.addEventListener('DOMContentLoaded', function () {
                // Data donut dari controller: [{ label, value, key }, ...] urut New → Lost
                const funnelData = @json($funnel);

                // Warna segmen donut — SAMAKAN dengan warna badge status di view lain.
                // Palet bawaan: new=blue, contacted=yellow, qualified=purple,
                // proposal=orange, won=green, lost=red. Ubah nilai hex-nya di sini.
                const statusColors = {
                    new:        '#3b82f6', // biru   (bg-blue-100 text-blue-800)
                    contacted:  '#eab308', // kuning (bg-yellow-100 text-yellow-800)
                    qualified:  '#a855f7', // ungu   (bg-purple-100 text-purple-800)
                    proposal:   '#f97316', // oranye (bg-orange-100 text-orange-800)
                    won:        '#22c55e', // hijau  (bg-green-100 text-green-800)
                    lost:       '#ef4444', // merah  (bg-red-100 text-red-800)
                };

                const isDark = document.documentElement.classList.contains('dark');

                new ApexCharts(document.querySelector('#status-donut-chart'), {
                    chart: {
                        type: 'donut',
                        height: 380,
                        width: '100%', // responsif mengikuti container
                        toolbar: { show: false },
                        background: 'transparent',
                    },
                    series: funnelData.map(s => s.value),
                    labels: funnelData.map(s => s.label),
                    colors: funnelData.map(s => statusColors[s.key]),
                    theme: { mode: isDark ? 'dark' : 'light' },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%', // rasio lubang tengah donut agar proporsional
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Lead',
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: isDark ? '#cbd5e1' : '#475569',
                                    },
                                },
                            },
                        },
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        fontSize: '13px',
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (val, opts) => opts.w.globals.series[opts.seriesIndex],
                    },
                }).render();
            });
        </script>
    </div>

    {{-- Ringkasan per status --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 mt-4">
        <h2 class="font-semibold text-slate-700 mb-4">Ringkasan per Status</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'] as $status)
                <a href="{{ route('leads.index', ['status' => $status]) }}"
                   class="group flex flex-col items-center justify-center gap-1.5 rounded-xl border p-4 text-center transition hover:scale-[1.02] active:scale-[0.99]
                    @switch($status)
                        @case('new') bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300 dark:hover:bg-blue-900/30 @break
                        @case('contacted') bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300 dark:hover:bg-amber-900/30 @break
                        @case('qualified') bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100 dark:bg-purple-900/20 dark:border-purple-800 dark:text-purple-300 dark:hover:bg-purple-900/30 @break
                        @case('proposal') bg-orange-50 border-orange-200 text-orange-700 hover:bg-orange-100 dark:bg-orange-900/20 dark:border-orange-800 dark:text-orange-300 dark:hover:bg-orange-900/30 @break
                        @case('won') bg-green-50 border-green-200 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300 dark:hover:bg-green-900/30 @break
                        @default bg-red-50 border-red-200 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-900/30
                    @endswitch
                   ">
                    <span class="text-2xl font-extrabold leading-none">{{ $statusCounts[$status] ?? 0 }}</span>
                    <span class="text-xs font-semibold uppercase tracking-wider">{{ ucfirst($status) }}</span>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
