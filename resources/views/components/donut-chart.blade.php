{{-- Donut chart SVG murni (pengganti ApexCharts pie/donut).
    Props: :data="[{label, value, color, key?}]", :size="250", :strokeWidth="30"
    Interaksi: hover NONAKTIF (diagram diam); klik kirim CustomEvent
    window donut-select dengan detail {key, label, value}.
--}}
@props(['data' => [], 'size' => 250, 'strokeWidth' => 30])

@php
$size = max(80, (int) $size);
$strokeWidth = max(8, min(60, (int) $strokeWidth));
$segments = collect($data)->map(fn ($s) => [
    'label' => (string) ($s['label'] ?? ''),
    'value' => (float) ($s['value'] ?? 0),
    'color' => (string) ($s['color'] ?? '#64748b'),
    'key' => (string) ($s['key'] ?? $s['label'] ?? ''),
])->filter(fn ($s) => $s['value'] > 0)->values();
$total = $segments->sum('value');
$radius = $size / 2 - $strokeWidth / 2;
$circ = 2 * M_PI * $radius;
$cum = 0.0;
@endphp

<svg
    viewBox="0 0 {{ $size }} {{ $size }}"
    class="donut-svg h-auto w-full -rotate-90 overflow-visible"
    role="img"
    aria-label="{{ __('Diagram donat') }}"
>
    {{-- Ring dasar --}}
    <circle
        cx="{{ $size / 2 }}"
        cy="{{ $size / 2 }}"
        r="{{ $radius }}"
        fill="transparent"
        stroke-width="{{ $strokeWidth }}"
        style="stroke: var(--card-border)"
    />
    {{-- Segmen data --}}
    @foreach($segments as $i => $seg)
        @php
            $pct = $total > 0 ? ($seg['value'] / $total) * 100 : 0;
            $dash = ($pct / 100) * $circ;
            $off = ($cum / 100) * $circ;
            $cum += $pct;
        @endphp
        <circle
            class="donut-seg"
            data-key="{{ $seg['key'] }}"
            data-label="{{ $seg['label'] }}"
            data-value="{{ $seg['value'] }}"
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="transparent"
            stroke="{{ $seg['color'] }}"
            stroke-width="{{ $strokeWidth }}"
            stroke-linecap="round"
            stroke-dasharray="{{ number_format($dash, 2, '.', '') }} {{ number_format($circ, 2, '.', '') }}"
            stroke-dashoffset="{{ number_format(-$off, 2, '.', '') }}"
            style="animation-delay: {{ $i * 70 }}ms"
        >
            <title>{{ $seg['label'] }} — {{ rtrim(rtrim(number_format($seg['value'], 1, '.', ''), '0'), '.') }}</title>
        </circle>
    @endforeach
</svg>

@once
<style>
    .donut-svg .donut-seg {
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
        transition: opacity 0.2s ease, filter 0.2s ease, transform 0.2s ease;
        animation: donut-seg-in 0.5s ease backwards;
    }
    /* Hover dimatikan: segmen tetap diam, sorotan hanya via klik (data-active) */
    /* Sorotan menetap (segmen diklik) — tanpa memudarkan segmen lain */
    .donut-svg .donut-seg[data-active="true"] {
        filter: brightness(1.12) saturate(1.2) drop-shadow(0 0 6px rgba(0, 0, 0, 0.25));
        transform: scale(1.03);
    }
    @keyframes donut-seg-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .donut-svg .donut-seg { animation: none; }
    }
</style>
<script>
    // ponytail: hanya klik (donut-select); hover sengaja tidak dipasang agar diagram diam
    (function () {
        if (window.__donutChartInit) return;
        window.__donutChartInit = true;

        function detailOf(seg) {
            return { key: seg.dataset.key, label: seg.dataset.label, value: Number(seg.dataset.value) };
        }
        function bind(svg) {
            if (svg.dataset.bound) return;
            svg.dataset.bound = '1';
            svg.querySelectorAll('.donut-seg').forEach(function (seg) {
                seg.addEventListener('click', function () {
                    window.dispatchEvent(new CustomEvent('donut-select', { detail: detailOf(seg) }));
                });
            });
        }
        function bindAll() {
            document.querySelectorAll('svg.donut-svg').forEach(bind);
        }
        bindAll();
        document.addEventListener('DOMContentLoaded', bindAll);
    })();
</script>
@endonce
