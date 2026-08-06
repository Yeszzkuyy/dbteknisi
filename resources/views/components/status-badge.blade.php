@props(['color' => 'slate', 'icon' => null])

@php
    $colors = [
        'green' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'orange' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'slate' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
    ];
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$color] ?? $colors['slate'] }}">
    @if($icon)<span>{{ $icon }}</span>@endif
    {{ $slot }}
</span>