<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Dashboard Marketing</h1>
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
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Lead Masuk — {{ $dateFrom }} s/d {{ $dateTo }}</h2>
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
            <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Lead per Sumber</h2>
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

    {{-- Donut Lead per Status (ApexCharts) + tabel dinamis --}}
    <div class="w-full bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 mt-4" x-data="{
        selectedStatus: null,
        leads: @js($leadsByStatus),
        select(status) {
            this.selectedStatus = this.selectedStatus === status ? null : status;
            this.applyMuted();
        },
        applyMuted() {
            const chart = window.marketingDonutChart;
            if (!chart) return;
            const slices = document.querySelectorAll('#status-donut-chart .apexcharts-pie-series path');
            const index = this.selectedStatus
                ? chart.w.globals.labels.map(l => l.toLowerCase()).indexOf(this.selectedStatus)
                : -1;
            slices.forEach((slice, i) => {
                slice.style.opacity = this.selectedStatus && i !== index ? '0.35' : '1';
            });
        }
    }">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">Pipeline Lead per Status</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 -mt-2 mb-4">Klik segmen untuk melihat detail lead pada status tersebut.</p>
        <div class="relative w-full max-w-[420px] mx-auto">
            <div id="status-donut-chart" class="w-full"></div>
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Total Lead</span>
                <span class="mt-0.5 text-3xl font-bold text-slate-800 tabular-nums dark:text-slate-100">{{ $stats['total'] }}</span>
            </div>
        </div>

        {{-- Tabel dinamis per status --}}
        <div x-cloak x-show="selectedStatus !== null" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
             class="mt-6 border-t border-slate-100 dark:border-slate-700 pt-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-slate-700 dark:text-slate-200">
                    Detail Lead <span class="text-blue-600 dark:text-blue-400 uppercase" x-text="selectedStatus"></span>
                    <span class="text-sm font-medium text-slate-400">(<span x-text="(leads[selectedStatus] || []).length"></span> lead)</span>
                </h3>
                <button type="button" @click="select(selectedStatus)"
                        class="text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
                    Tutup
                </button>
            </div>

            <template x-if="(leads[selectedStatus] || []).length > 0">
                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Lead / Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Sumber</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Partner</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Tanggal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            <template x-for="lead in leads[selectedStatus]" :key="lead.id">
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-800 dark:text-slate-100" x-text="lead.name"></p>
                                        <p class="text-xs text-slate-500" x-show="lead.company" x-text="lead.company"></p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300 capitalize" x-text="lead.source"></td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300" x-text="lead.partner"></td>
                                    <td class="px-4 py-3 text-right text-slate-500 whitespace-nowrap" x-text="lead.date"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <template x-if="(leads[selectedStatus] || []).length === 0">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-900/40 px-5 py-8 text-center">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada lead berstatus <span class="uppercase" x-text="selectedStatus"></span> pada rentang tanggal ini.</p>
                </div>
            </template>
        </div>

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

                window.marketingDonutChart = new ApexCharts(document.querySelector('#status-donut-chart'), {
                    chart: {
                        type: 'donut',
                        height: 380,
                        width: '100%', // responsif mengikuti container
                        toolbar: { show: false },
                        background: 'transparent',
                        events: {
                            dataPointSelection: (event, chartContext, config) => {
                                const status = config.w.config.labels[config.dataPointIndex].toLowerCase();
                                // Akses state Alpine via scope dari elemen dengan x-data yang membungkus donut
                                const scope = Alpine.$data(document.querySelector('#status-donut-chart').closest('[x-data]'));
                                scope.select(status);
                            }
                        },
                    },
                    series: funnelData.map(s => s.value),
                    labels: funnelData.map(s => s.label),
                    colors: funnelData.map(s => statusColors[s.key]),
                    theme: { mode: isDark ? 'dark' : 'light' },
                    stroke: { width: 3, colors: [isDark ? '#1e293b' : '#ffffff'] },
                    fill: { type: 'solid' },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%', // rasio lubang tengah donut agar proporsional
                            },
                        },
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        fontSize: '13px',
                        formatter: (label, opts) => `${label} — ${opts.w.globals.series[opts.seriesIndex]} lead`,
                    },
                    dataLabels: { enabled: false },
                }).render();
            });
        </script>
    </div>
</x-app-layout>
