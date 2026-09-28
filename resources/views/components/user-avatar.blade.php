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
    // Ring + hover glow selalu mengikuti warna tema aktif (accent).
    // Yeski: ring netral agar efek running terlihat jelas.
    $ring = $running ? 'ring-slate-900/80' : 'ring-accent-500';
    $glow = 'hover:shadow-accent-500/50';
@endphp

<span class="relative inline-flex shrink-0 self-center aspect-square items-center justify-center">
@if ($running)
    {{-- Dua warna tema saling mengejar (looping), mengikuti html[data-theme] + dark mode --}}
    <span aria-hidden="true"
          class="absolute -inset-1 rounded-full animate-spin [animation-duration:2.5s] motion-reduce:animate-none bg-[conic-gradient(from_0deg,rgb(var(--accent-500))_0deg,transparent_100deg,rgb(var(--accent-300))_180deg,transparent_280deg,rgb(var(--accent-500))_360deg)] shadow-[0_0_12px_2px_rgb(var(--accent-500)/0.45)]"></span>
@endif
@if ($canZoom)
    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" width="80" height="80" loading="lazy" decoding="async"
         x-on:click="$dispatch('view-avatar', { src: @js($photoUrl), name: @js($user->name ?? '') })"
         class="{{ $size }} rounded-full aspect-square object-cover shrink-0 cursor-zoom-in transition ring-2 {{ $ring }} hover:shadow-lg {{ $glow }} {{ $class }} relative"
         title="Lihat foto profil" role="button" tabindex="0"
         x-on:keydown.enter="$dispatch('view-avatar', { src: @js($photoUrl), name: @js($user->name ?? '') })">
@elseif($photoUrl)
    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" width="80" height="80" loading="lazy" decoding="async"
         class="{{ $size }} rounded-full aspect-square object-cover shrink-0 ring-2 {{ $ring }} {{ $class }} relative">
@else
    <div class="{{ $size }} rounded-full {{ $colors[$color] }} flex items-center justify-center shrink-0 ring-2 {{ $ring }} {{ $class }} relative">
        <span class="{{ $text }} font-semibold">{{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}</span>
    </div>
@endif
</span>
