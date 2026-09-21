{{-- Donut chart SVG murni (pengganti ApexCharts pie/donut).
    Props: :data="[{label, value, color, key?}]", :size="250", :strokeWidth="30",
    :scroll="false" (true = sweep ditahan sampai ancestor [data-reveal].in-view)
    Interaksi: hover NONAKTIF (diagram diam); klik kirim CustomEvent
    window donut-select dengan detail {key, label, value}.
--}}
@props(['data' => [], 'size' => 250, 'strokeWidth' => 30, 'scroll' => false])

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
$gap = 3; // celah antar-segmen (px keliling) agar warna tidak bleed
$cum = 0.0;
@endphp

<svg
    viewBox="0 0 {{ $size }} {{ $size }}"
    class="donut-svg h-auto w-full -rotate-90 overflow-visible{{ $scroll ? ' donut-scroll' : '' }}"
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
            $dash = max(($pct / 100) * $circ - $gap, 1);
            $off = ($cum / 100) * $circ;
            $cum += $pct;
            $valLabel = rtrim(rtrim(number_format($seg['value'], 1, '.', ''), '0'), '.');
        @endphp
        <circle
            class="donut-seg"
            data-key="{{ $seg['key'] }}"
            data-label="{{ $seg['label'] }}"
            data-value="{{ $seg['value'] }}"
            tabindex="0"
            role="button"
            aria-label="{{ $seg['label'] }}: {{ $valLabel }} ({{ round($pct) }}%)"
            cx="{{ $size / 2 }}"
            cy="{{ $size / 2 }}"
            r="{{ $radius }}"
            fill="transparent"
            stroke="{{ $seg['color'] }}"
            stroke-width="{{ $strokeWidth }}"
            stroke-linecap="butt"
            stroke-dasharray="{{ number_format($dash, 2, '.', '') }} {{ number_format($circ, 2, '.', '') }}"
            stroke-dashoffset="{{ number_format(-$off, 2, '.', '') }}"
            style="--off: {{ number_format(-$off, 2, '.', '') }}; --c: {{ number_format($circ, 2, '.', '') }}; animation-delay: {{ $i * 160 }}ms"
        >
            <title>{{ $seg['label'] }} — {{ $valLabel }}</title>
        </circle>
    @endforeach
</svg>

@once
<style>
    .donut-svg .donut-seg {
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
        transition: filter 0.2s ease, transform 0.2s ease;
        animation: donut-seg-sweep 1.1s ease backwards;
    }
    /* Hover dimatikan: segmen tetap diam, sorotan hanya via klik/fokus (data-active) */
    .donut-svg .donut-seg:focus { outline: none; }
    /* Sorotan menetap (segmen diklik / fokus keyboard) — tanpa memudarkan segmen lain */
    .donut-svg .donut-seg[data-active="true"],
    .donut-svg .donut-seg:focus-visible {
        filter: brightness(1.1) saturate(1.15) drop-shadow(0 0 4px rgba(0, 0, 0, 0.22));
        transform: scale(1.03);
    }
    @keyframes donut-seg-sweep {
        from { stroke-dashoffset: var(--c); opacity: 0; }
        to { stroke-dashoffset: var(--off); opacity: 1; }
    }
    /* Mode scroll: sweep ditahan (pause di frame awal = tak kasat mata)
       sampai ancestor [data-reveal] dapat .in-view dari observer layout */
    .donut-svg.donut-scroll .donut-seg { animation-play-state: paused; }
    [data-reveal].in-view .donut-svg.donut-scroll .donut-seg { animation-play-state: running; }
    @media (prefers-reduced-motion: reduce) {
        .donut-svg .donut-seg { animation: none; }
    }
</style>
<noscript><style>.donut-svg.donut-scroll .donut-seg { animation: none !important; opacity: 1 !important; }</style></noscript>
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
                seg.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        seg.click();
                    }
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
