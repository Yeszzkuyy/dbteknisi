@props(['user' => null, 'size' => 'w-10 h-10', 'text' => 'text-sm', 'color' => 'blue', 'class' => '', 'clickable' => null])

@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
        'indigo' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-300',
    ];

    $photoUrl = ($user && $user->avatar) ? asset('storage/' . $user->avatar) : null;
    // clickable: null = otomatis (aktif jika ada foto), false = dimatikan (mis. avatar dropdown header)
    $canZoom = $photoUrl !== null && $clickable !== false;
    // Border animasi khusus founder (Yeski) — tampil di semua pemakaian komponen.
    $running = (bool) ($user?->hasAnimatedAvatarBorder() ?? false);
@endphp

<span class="relative inline-flex shrink-0 self-center aspect-square items-center justify-center">
@if ($running)
    <span aria-hidden="true"
          class="absolute -inset-1 rounded-full bg-[conic-gradient(from_0deg,#f59e0b,#ec4899,#8b5cf6,#22d3ee,#f59e0b)] animate-spin [animation-duration:3s] motion-reduce:animate-none"></span>
@endif
@if ($canZoom)
    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" width="80" height="80" loading="lazy" decoding="async"
         x-on:click="$dispatch('view-avatar', { src: @js($photoUrl), name: @js($user->name ?? '') })"
         class="{{ $size }} rounded-full aspect-square object-cover shrink-0 cursor-zoom-in transition hover:ring-2 hover:ring-accent-300 {{ $class }} relative"
         title="Lihat foto profil" role="button" tabindex="0"
         x-on:keydown.enter="$dispatch('view-avatar', { src: @js($photoUrl), name: @js($user->name ?? '') })">
@elseif($photoUrl)
    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" width="80" height="80" loading="lazy" decoding="async"
         class="{{ $size }} rounded-full aspect-square object-cover shrink-0 {{ $class }} relative">
@else
    <div class="{{ $size }} rounded-full {{ $colors[$color] }} flex items-center justify-center shrink-0 {{ $class }} relative">
        <span class="{{ $text }} font-semibold">{{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}</span>
    </div>
@endif
</span>
