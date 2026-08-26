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

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <p class="text-sm text-slate-500">Lead Bulan Ini</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['this_month'] }}</p>
            <p class="text-xs mt-1 {{ $stats['this_month'] >= $stats['last_month'] ? 'text-green-600' : 'text-red-500' }}">
                {{ $stats['this_month'] >= $stats['last_month'] ? '▲' : '▼' }} bulan lalu: {{ $stats['last_month'] }}
            </p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <p class="text-sm text-slate-500">Total Lead</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <p class="text-sm text-slate-500">Lead Aktif</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <p class="text-sm text-slate-500">Won</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['won'] }}</p>
            <p class="text-xs text-red-500 mt-1">Lost: {{ $stats['lost'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-5">
            <p class="text-sm text-slate-500">Conversion Rate</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['conversion'] }}%</p>
            <p class="text-xs text-slate-400 mt-1">won ÷ (won + lost)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Tren lead masuk --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6">
            <h2 class="font-semibold text-slate-700 mb-4">Lead Masuk — 6 Bulan Terakhir</h2>
            @php($maxTrend = max($trend->max('total'), 1))
            <div class="flex items-end justify-between gap-3 h-44">
                @foreach($trend as $month)
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <span class="text-xs font-semibold text-slate-600">{{ $month->total }}</span>
                        <div class="w-full max-w-12 rounded-t-lg bg-blue-500 transition-all"
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

    {{-- Ringkasan per status --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-600 p-6 mt-4">
        <div class="flex flex-wrap gap-3">
            @foreach(['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'] as $status)
                <a href="{{ route('leads.index', ['status' => $status]) }}"
                   class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                    @switch($status)
                        @case('new') bg-blue-100 text-blue-800 @break
                        @case('contacted') bg-yellow-100 text-yellow-800 @break
                        @case('qualified') bg-purple-100 text-purple-800 @break
                        @case('proposal') bg-orange-100 text-orange-800 @break
                        @case('won') bg-green-100 text-green-800 @break
                        @default bg-red-100 text-red-800
                    @endswitch
                ">
                    {{ ucfirst($status) }}: {{ $statusCounts[$status] ?? 0 }}
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
