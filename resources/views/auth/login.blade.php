<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell min-h-screen text-[#E4E8ED] antialiased">

<div class="flex min-h-screen items-center justify-center p-4 sm:p-6">

    {{-- ============ CARD ============ --}}
    <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-[#3B4552] bg-[#1E2530] lg:grid lg:grid-cols-[290px_1fr]">

        {{-- ============ KOLOM KIRI: FORM ============ --}}
        <main class="p-6 sm:p-8">
            <div class="w-full">

                {{-- Logo + perusahaan --}}
                <div class="flex items-center gap-2.5">
                    <span class="relative inline-flex h-8 w-8 rounded-lg bg-[#34C3A6]">
                        <span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-[#E85C4A]"></span>
                    </span>
                    <span class="text-sm font-medium text-[#E4E8ED]">3DY Group</span>
                </div>

                {{-- Produk + tagline --}}
                <div class="mt-5">
                    <p class="text-sm font-medium text-[#E4E8ED]">Tridaya App</p>
                    <p class="mt-0.5 text-xs text-[#8B96A5]">Full visibility, zero guesswork.</p>
                </div>

                {{-- Sapaan --}}
                <h1 class="mt-7 text-xl font-medium text-[#E4E8ED]">Welcome back</h1>
                <p class="mt-1 text-sm text-[#8B96A5]">Sign in to continue to your dashboard.</p>

                <x-auth-session-status class="mt-4 text-sm !text-[#34C3A6]" :status="session('status')" />

                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="mt-6"
                    x-data="{ submitting: false, done: false }"
                    x-on:submit="if (done) return; $event.preventDefault(); submitting = true; window.setTimeout(() => { done = true }, 650); window.setTimeout(() => $el.submit(), 1050)"
                >
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-medium text-[#8B96A5]">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nama@perusahaan.com"
                            class="auth-input h-10 px-3 text-sm"
                        >
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 !text-[#E85C4A]" />
                    </div>

                    {{-- Password --}}
                    <div class="mt-4" x-data="{ showPassword: false }">
                        <label for="password" class="mb-1.5 block text-xs font-medium text-[#8B96A5]">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="auth-input h-10 pl-3 pr-10 text-sm"
                            >
                            <button
                                type="button"
                                x-on:click="showPassword = !showPassword"
                                :aria-pressed="showPassword"
                                aria-label="Tampilkan atau sembunyikan password"
                                class="absolute right-1 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-[#8B96A5] transition hover:text-[#E4E8ED] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#34C3A6]"
                            >
                                <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 !text-[#E85C4A]" />
                    </div>

                    {{-- Remember + forgot --}}
                    <div class="mt-4 flex items-center justify-between text-xs">
                        <label class="inline-flex cursor-pointer select-none items-center gap-2 text-[#8B96A5]">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#3B4552] bg-[#141922] text-[#34C3A6] accent-[#34C3A6] focus:ring-[#34C3A6]">
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-[#34C3A6] transition hover:text-[#2FAE93] focus:outline-none focus-visible:underline">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="mt-5 flex h-10 w-full items-center justify-center rounded-[10px] bg-[#34C3A6] text-sm font-medium text-[#141922] transition hover:bg-[#2FAE93] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#34C3A6] focus-visible:ring-offset-2 focus-visible:ring-offset-[#1E2530] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <template x-if="!submitting">
                                <span class="flex items-center gap-2">Sign in</span>
                            </template>
                            <template x-if="submitting && !done">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.3" stroke-width="4"/>
                                        <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                    </svg>
                                    Signing in…
                                </span>
                            </template>
                            <template x-if="done">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                                    </svg>
                                    Signed in
                                </span>
                            </template>
                        </span>
                    </button>
                </form>

                {{-- Footer --}}
                <p class="mt-6 text-xs text-[#8B96A5]">© {{ date('Y') }} Tridaya App</p>
            </div>
        </main>

        {{-- ============ KOLOM KANAN: ILUSTRASI FLAT ============ --}}
        <aside class="relative hidden items-center justify-center border-l border-[#3B4552] p-8 lg:flex" aria-hidden="true">
            <div class="relative h-[400px] w-[380px]">

                {{-- Panel dashboard vertikal --}}
                <div class="absolute left-1/2 top-1/2 h-[270px] w-[210px] -translate-x-1/2 -translate-y-1/2 rounded-[14px] border border-[#3B4552] bg-[#262E3A] p-4">
                    {{-- badge centang --}}
                    <div class="absolute -top-5 left-1/2 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#3B4552] bg-[#1E2530]">
                        <svg class="h-5 w-5 text-[#34C3A6]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>

                    {{-- status live --}}
                    <div class="mt-5 flex items-center justify-between">
                        <span class="auth-mono text-[10px] text-[#8B96A5]">3dy_monitor</span>
                        <span class="flex items-center gap-1.5">
                            <span class="live-dot h-1.5 w-1.5 rounded-full bg-[#34C3A6]"></span>
                            <span class="auth-mono text-[10px] text-[#8B96A5]">live</span>
                        </span>
                    </div>

                    {{-- garis data --}}
                    <div class="mt-4 space-y-2.5">
                        <div class="h-2 w-11/12 rounded-full bg-[#3B4552]"></div>
                        <div class="h-2 w-3/4 rounded-full bg-[#3B4552]"></div>
                        <div class="h-2 w-5/6 rounded-full bg-[#3B4552]"></div>
                        <div class="h-2 w-2/3 rounded-full bg-[#3B4552]"></div>
                        <div class="h-2 w-4/5 rounded-full bg-[#3B4552]"></div>
                    </div>
                </div>

                {{-- Kartu mengambang: video & audio --}}
                <div class="absolute left-0 top-0 flex items-center gap-2 rounded-[10px] border border-[#3B4552] bg-[#262E3A] px-2.5 py-2">
                    <svg class="h-4 w-4 shrink-0 text-[#34C3A6]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-2.36a.75.75 0 011.03.67v6.38a.75.75 0 01-1.03.67l-4.72-2.36m0-3v3m0-3v-1.5a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9.75v4.5A2.25 2.25 0 005.25 16.5h9a2.25 2.25 0 002.25-2.25v-1.5z" />
                    </svg>
                    <span class="whitespace-nowrap text-[11px] text-[#E4E8ED]">Video &amp; audio</span>
                </div>

                {{-- Kartu mengambang: server --}}
                <div class="absolute right-0 top-10 flex items-center gap-2 rounded-[10px] border border-[#3B4552] bg-[#262E3A] px-2.5 py-2">
                    <svg class="h-4 w-4 shrink-0 text-[#34C3A6]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3V6a3 3 0 013-3h13.5a3 3 0 013 3v5.25a3 3 0 01-3 3m-13.5 0v5.25a3 3 0 003 3h7.5a3 3 0 003-3V14.25M6.75 7.5h.008v.008H6.75V7.5zm0 9h.008v.008H6.75V16.5z" />
                    </svg>
                    <span class="whitespace-nowrap text-[11px] text-[#E4E8ED]">Server</span>
                </div>

                {{-- Kartu mengambang: security --}}
                <div class="absolute left-0 top-1/2 flex -translate-y-1/2 items-center gap-2 rounded-[10px] border border-[#3B4552] bg-[#262E3A] px-2.5 py-2">
                    <svg class="h-4 w-4 shrink-0 text-[#34C3A6]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span class="whitespace-nowrap text-[11px] text-[#E4E8ED]">Security</span>
                </div>

                {{-- Kartu mengambang: electrical --}}
                <div class="absolute right-0 bottom-24 flex items-center gap-2 rounded-[10px] border border-[#3B4552] bg-[#262E3A] px-2.5 py-2">
                    <svg class="h-4 w-4 shrink-0 text-[#34C3A6]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span class="whitespace-nowrap text-[11px] text-[#E4E8ED]">Electrical</span>
                </div>

                {{-- Siluet orang menunjuk ke panel --}}
                <svg class="absolute bottom-0 right-10 h-[92px] w-[68px]" viewBox="0 0 80 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="14" r="11" fill="#4A5563"/>
                    <path d="M40 26c-10 0-17 7-17 17v31h34V43c0-10-7-17-17-17z" fill="#4A5563"/>
                    <rect x="4" y="44" width="32" height="10" rx="5" fill="#4A5563"/>
                    <rect x="26" y="74" width="11" height="32" rx="5" fill="#4A5563"/>
                    <rect x="43" y="74" width="11" height="32" rx="5" fill="#4A5563"/>
                </svg>
            </div>
        </aside>
    </div>
</div>

</body>
</html>
