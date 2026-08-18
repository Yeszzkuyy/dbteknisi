@props(['title'])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
    @if($slot->isNotEmpty())
        <div class="shrink-0">{{ $slot }}</div>
    @endif
</div>
