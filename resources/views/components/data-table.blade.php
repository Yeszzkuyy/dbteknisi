<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-slate-200 dark:divide-slate-600']) }}>
        {{ $slot }}
    </table>
</div>
