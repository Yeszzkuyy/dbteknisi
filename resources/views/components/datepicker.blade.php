@props(['disabled' => false])

<input type="date" {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-300 bg-white text-slate-800 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 cursor-pointer']) }} @disabled($disabled)
       onfocus="this.showPicker()">
