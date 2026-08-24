@props(['user' => null, 'size' => 'w-10 h-10', 'text' => 'text-sm', 'color' => 'blue', 'class' => ''])

@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
        'indigo' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-300',
    ];
@endphp

@if($user && $user->avatar)
    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
         class="{{ $size }} rounded-full object-cover shrink-0 {{ $class }}">
@else
    <div class="{{ $size }} rounded-full {{ $colors[$color] }} flex items-center justify-center shrink-0 {{ $class }}">
        <span class="{{ $text }} font-semibold">{{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}</span>
    </div>
@endif