@props([
    'message' => '',
    'timeout' => 2000,
])

<div x-data="{ open: true }" x-show="open" x-cloak
     x-init="setTimeout(() => open = false, {{ (int) $timeout }})"
     class="status-card-backdrop fixed inset-0 z-[70] flex items-center justify-center p-4"
     style="background: rgba(2, 6, 23, .55);" role="status">
    <div class="status-card-pop w-full max-w-sm rounded-2xl bg-white dark:bg-slate-800 px-10 py-8 text-center shadow-xl">
        <svg class="mx-auto" width="80" height="80" viewBox="0 0 72 72" fill="none" aria-hidden="true">
            <circle class="status-check__circle" cx="36" cy="36" r="34" stroke="#22c55e" stroke-width="5" stroke-linecap="round" />
            <path class="status-check__mark" d="M22 37l10 10 20-22" stroke="#22c55e" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p class="mt-4 font-semibold text-slate-800 dark:text-slate-100">{{ $message }}</p>
    </div>
</div>
