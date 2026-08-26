@props(['tip'])

<span {{ $attributes->merge(['class' => 'relative inline-flex items-center group align-middle cursor-help']) }} tabindex="0">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
         stroke="currentColor" class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
    </svg>
    <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 w-56
                 bg-slate-900 dark:bg-slate-700 text-white text-xs leading-relaxed rounded-lg px-3 py-2
                 opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity z-30 shadow-lg">
        {{ $tip }}
    </span>
</span>
