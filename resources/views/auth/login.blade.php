<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell min-h-screen font-sans text-white antialiased">

<div class="relative min-h-screen w-full lg:grid lg:grid-cols-[45%_55%]">

    {{-- ============ PANEL VISUAL: THE LIVING NETWORK ============ --}}
    <aside
        aria-hidden="true"
        class="absolute inset-0 overflow-hidden lg:relative lg:order-2"
        x-data="{
            rx: 0, ry: 0, rm: true,
            init() { this.rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
        }"
        x-on:mousemove.window="if (!rm) { rx = ($event.clientX / window.innerWidth - 0.5); ry = ($event.clientY / window.innerHeight - 0.5); }"
    >
        {{-- glow dasar --}}
        <div class="absolute inset-0" style="background: radial-gradient(60% 60% at 62% 45%, rgba(37,99,235,0.22) 0%, transparent 70%);"></div>

        <div class="absolute inset-0 flex items-center justify-center"
             :style="rm ? '' : `transform: translate(${rx * 14}px, ${ry * 14}px)`"
             style="transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); transform-style: preserve-3d;">

            <svg class="net-drift w-[88%] max-w-[560px]" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="hubGlow" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="rgb(37,99,235)" stop-opacity="0.45"/>
                        <stop offset="100%" stop-color="rgb(37,99,235)" stop-opacity="0"/>
                    </radialGradient>
                </defs>

                <circle cx="300" cy="300" r="250" fill="url(#hubGlow)"/>

                {{-- orbit berputar --}}
                <g class="net-spin">
                    <circle cx="300" cy="300" r="232" stroke="rgba(37,99,235,0.20)" stroke-width="1" stroke-dasharray="2 14"/>
                    <circle cx="300" cy="300" r="205" stroke="rgba(37,99,235,0.10)" stroke-width="1"/>
                </g>
                <circle cx="300" cy="300" r="150" stroke="rgba(37,99,235,0.12)" stroke-width="1"/>

                {{-- jalur data --}}
                <line class="net-link" x1="300" y1="300" x2="300" y2="118"/>
                <line class="net-link alt" x1="300" y1="300" x2="482" y2="300"/>
                <line class="net-link" x1="300" y1="300" x2="300" y2="482"/>
                <line class="net-link alt" x1="300" y1="300" x2="118" y2="300"/>

                {{-- hub 3DY --}}
                <circle class="net-node d4" cx="300" cy="300" r="52" fill="rgb(37,99,235)"/>
                <circle cx="300" cy="300" r="52" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>
                <text x="300" y="307" text-anchor="middle" font-size="19" font-weight="700" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif">3DY</text>

                {{-- node NTI (atas) --}}
                <g>
                    <circle class="net-node" cx="300" cy="112" r="34" fill="#0A0A0A" stroke="rgb(37,99,235)" stroke-width="1.5"/>
                    <text x="300" y="118" text-anchor="middle" font-size="14" font-weight="700" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif">NTI</text>
                    <text x="300" y="170" text-anchor="middle" font-size="11" fill="rgba(255,255,255,0.45)" font-family="'Plus Jakarta Sans', sans-serif">Video &amp; Audio</text>
                </g>

                {{-- node MGK (kanan) --}}
                <g>
                    <circle class="net-node d1" cx="488" cy="300" r="34" fill="#0A0A0A" stroke="rgb(37,99,235)" stroke-width="1.5"/>
                    <text x="488" y="306" text-anchor="middle" font-size="14" font-weight="700" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif">MGK</text>
                    <text x="488" y="356" text-anchor="middle" font-size="11" fill="rgba(255,255,255,0.45)" font-family="'Plus Jakarta Sans', sans-serif">Security</text>
                </g>

                {{-- node TPS (bawah) --}}
                <g>
                    <circle class="net-node d2" cx="300" cy="488" r="34" fill="#0A0A0A" stroke="rgb(37,99,235)" stroke-width="1.5"/>
                    <text x="300" y="494" text-anchor="middle" font-size="14" font-weight="700" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif">TPS</text>
                    <text x="300" y="546" text-anchor="middle" font-size="11" fill="rgba(255,255,255,0.45)" font-family="'Plus Jakarta Sans', sans-serif">Electrical</text>
                </g>

                {{-- node WANi (kiri) --}}
                <g>
                    <circle class="net-node d3" cx="112" cy="300" r="34" fill="#0A0A0A" stroke="rgb(37,99,235)" stroke-width="1.5"/>
                    <text x="112" y="306" text-anchor="middle" font-size="14" font-weight="700" fill="#ffffff" font-family="'Plus Jakarta Sans', sans-serif">WANi</text>
                    <text x="112" y="356" text-anchor="middle" font-size="11" fill="rgba(255,255,255,0.45)" font-family="'Plus Jakarta Sans', sans-serif">Server</text>
                </g>
            </svg>
        </div>

        {{-- overlay gelap khusus mobile --}}
        <div class="absolute inset-0 bg-[rgba(10,10,10,0.86)] lg:hidden"></div>

        {{-- gradient blend ke sisi form (desktop) --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 hidden w-40 bg-gradient-to-r from-[#0A0A0A] to-transparent lg:block"></div>
    </aside>

    {{-- ============ PANEL FORM ============ --}}
    <main
        class="relative z-10 flex min-h-screen items-center justify-center px-5 py-10 sm:px-8 lg:order-1 lg:px-14"
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 80)"
    >
        <div class="w-full max-w-md">

            {{-- Branding --}}
            <div class="mb-8 flex flex-col items-center text-center"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-6"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div x-data="{ logoFailed: false }" class="mb-4">
                    <img
                        x-show="!logoFailed"
                        x-on:error="logoFailed = true"
                        src="{{ asset('images/logo/logo.png') }}"
                        alt="{{ config('app.name', 'Tridaya App') }}"
                        class="h-14 w-auto object-contain"
                    >
                    <div
                        x-show="logoFailed"
                        x-cloak
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-[rgb(var(--accent))] text-xl font-bold text-white shadow-[0_10px_30px_-10px_rgba(37,99,235,0.7)]"
                    >3</div>
                </div>
                <h1 class="text-2xl font-bold tracking-[-0.02em]">{{ config('app.name', 'Tridaya App') }}</h1>
                <p class="mt-1 text-sm text-white/55">Full Visibility, Zero Guesswork</p>
            </div>

            {{-- Sapaan --}}
            <div class="mb-6 text-center"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-700 delay-150"
                 x-transition:enter-start="opacity-0 translate-y-6"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <h2 class="text-lg font-semibold">Welcome Back</h2>
                <p class="mt-1 text-sm text-white/45">Sign in to continue to your dashboard</p>
            </div>

            <x-auth-session-status class="mb-4 text-center !text-green-400" :status="session('status')" />

            <form
                method="POST"
                action="{{ route('login') }}"
                class="space-y-6"
                x-data="{ submitting: false, done: false }"
                x-on:submit="if (done) return; $event.preventDefault(); submitting = true; window.setTimeout(() => { done = true }, 700); window.setTimeout(() => $el.submit(), 1150)"
            >
                @csrf

                {{-- Email --}}
                <div class="relative"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-700 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-6"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <svg class="pointer-events-none absolute left-0 top-1/2 h-5 w-5 -translate-y-1/2 text-[rgba(var(--accent),0.9)]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder=" "
                        class="auth-input peer w-full pl-8 pr-3 pt-6 pb-2 text-sm text-white sm:text-base"
                    >
                    <label for="email"
                        class="pointer-events-none absolute left-8 top-1 text-xs text-white/45 transition-all duration-300
                               peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm
                               peer-focus:top-1 peer-focus:text-xs peer-focus:text-[rgb(var(--accent))]">
                        Email Address
                    </label>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 !text-red-400" />
                </div>

                {{-- Password --}}
                <div class="relative" x-data="{ showPassword: false }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-700 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-6"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <svg class="pointer-events-none absolute left-0 top-1/2 h-5 w-5 -translate-y-1/2 text-[rgba(var(--accent),0.9)]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                        class="auth-input peer w-full pl-8 pr-10 pt-6 pb-2 text-sm text-white sm:text-base"
                    >
                    <label for="password"
                        class="pointer-events-none absolute left-8 top-1 text-xs text-white/45 transition-all duration-300
                               peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm
                               peer-focus:top-1 peer-focus:text-xs peer-focus:text-[rgb(var(--accent))]">
                        Password
                    </label>
                    <button
                        type="button"
                        x-on:click="showPassword = !showPassword"
                        :aria-pressed="showPassword"
                        aria-label="Tampilkan atau sembunyikan password"
                        class="absolute right-0 top-1/2 -translate-y-1/2 rounded-md p-1 text-white/45 transition hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[rgb(var(--accent))]"
                    >
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 !text-red-400" />
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between text-sm"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-700 delay-450"
                     x-transition:enter-start="opacity-0 translate-y-6"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="inline-flex cursor-pointer select-none items-center gap-2 text-white/75">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/30 bg-transparent accent-[rgb(var(--accent))] focus:ring-[rgb(var(--accent))]">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-medium text-[rgb(var(--accent))] transition hover:opacity-80 focus:outline-none focus-visible:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    :disabled="submitting"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-700 delay-450"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="auth-btn w-full rounded-xl bg-[rgb(var(--accent))] py-3 font-semibold text-white transition duration-300 hover:-translate-y-px hover:bg-[rgb(var(--accent-strong))] hover:shadow-[0_16px_34px_-12px_rgba(37,99,235,0.75)] focus:outline-none focus-visible:ring-4 focus-visible:ring-[rgba(var(--accent),0.35)] disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0 disabled:hover:shadow-none"
                >
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <template x-if="!submitting">
                            <span class="flex items-center gap-2">
                                Sign In
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </span>
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
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Berhasil
                            </span>
                        </template>
                    </span>
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-white/35">
                © {{ date('Y') }} {{ config('app.name', 'Tridaya App') }}. All rights reserved.
            </p>
        </div>
    </main>
</div>

</body>
</html>
