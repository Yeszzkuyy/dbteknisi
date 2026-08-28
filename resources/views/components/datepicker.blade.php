@props(['disabled' => false])

<div class="relative" x-data="{ show: false }">
    <input type="date" {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer']) }} @disabled($disabled)
           @click="show = true" x-on:blur="show = false">
</div>
