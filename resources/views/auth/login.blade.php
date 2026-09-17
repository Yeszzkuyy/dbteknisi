<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/kinetic-grid.js'])

    <style>
        [x-cloak] { display: none !important; }

        /* ============================================================
           TOKEN & RESET
           ============================================================ */
        :root {
            --login-blue:     #2563eb;
            --login-blue-mid: #3b52d9;
            --login-ink:      #111827;
            --login-muted:    #6b7280;
            --login-border:   #d1d5db;
            --login-paper:    #f8fafc;
            /* Panel ilustrasi — sesuai referensi: biru tua ke ungu medium */
            --panel-from:     #1a1464;
            --panel-mid:      #2d27b8;
            --panel-to:       #4338ca;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ============================================================
           LAYOUT UTAMA
           ============================================================ */
        .login-page {
            min-height: 100dvh;
            display: flex;
        }

        .login-shell {
            display: grid;
            width: 100%;
            min-height: 100dvh;
            /* ~40% form | ~60% ilustrasi, cocok dengan referensi */
            grid-template-columns: 40% 60%;
            overflow: hidden;
            background: #ffffff;
        }

        /* ============================================================
           PANEL KIRI — FORM
           ============================================================ */
        .login-form-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem clamp(1.5rem, 6vw, 3.5rem);
            background: #ffffff;
            isolation: isolate;
        }

        .login-form-panel::before {
            position: absolute;
            inset: 0;
            z-index: 0;
            content: '';
            background-image:
                radial-gradient(circle at 10% 15%, rgba(99,102,241,.09) 0 1px, transparent 1.5px),
                radial-gradient(circle at 85% 80%, rgba(236,72,153,.07) 0 1px, transparent 1.5px);
            background-size: 32px 32px, 44px 44px;
            opacity: .5;
            pointer-events: none;
        }

        .login-form-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 22rem);
        }

        /* Brand / logo */
        .login-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 2rem;
        }

        .login-brand-mark {
            width: 2rem;
            height: 2rem;
            border-radius: .45rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--login-blue);
            flex-shrink: 0;
        }

        .login-brand-mark img { width: 100%; height: 100%; object-fit: contain; }

        .login-brand-fallback {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .login-header {
            margin-bottom: 1.75rem;
        }

        .login-header h1 {
            font-size: clamp(1.25rem, 2.5vw, 1.55rem);
            font-weight: 700;
            color: var(--login-ink);
            line-height: 1.25;
            margin-bottom: .35rem;
        }

        .login-header p {
            font-size: .875rem;
            color: var(--login-muted);
            line-height: 1.5;
        }

        /* Form */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .login-field {
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .login-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .login-label {
            font-size: .8125rem;
            font-weight: 600;
            color: var(--login-ink);
        }

        .login-link {
            font-size: .8rem;
            font-weight: 500;
            color: var(--login-blue);
            text-decoration: none;
        }

        .login-link:hover { text-decoration: underline; }

        .login-input {
            width: 100%;
            min-height: 2.75rem;
            padding: .625rem .875rem;
            font-size: .875rem;
            font-family: inherit;
            color: var(--login-ink);
            background: #fff;
            border: 1px solid var(--login-border);
            border-radius: .5rem;
            outline: none;
            transition: border-color 200ms, box-shadow 200ms;
        }

        .login-input::placeholder { color: #9ca3af; }

        .login-input:focus {
            border-color: var(--login-blue);
            box-shadow: 0 0 0 3px rgba(37,99,235,.14);
        }

        .login-input-wrap { position: relative; }

        .login-input-password { padding-right: 2.75rem; }

        .login-password-toggle {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--login-muted);
            padding: .15rem;
            display: flex;
            align-items: center;
        }

        .login-password-toggle:hover { color: var(--login-ink); }

        .login-remember {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8125rem;
            color: var(--login-ink);
            cursor: pointer;
            user-select: none;
        }

        .login-remember input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: var(--login-blue);
            border-radius: .2rem;
            cursor: pointer;
            flex-shrink: 0;
        }

        .login-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            min-height: 2.85rem;
            padding: .65rem 1.25rem;
            font-size: .9rem;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            background: var(--login-blue);
            border: none;
            border-radius: .55rem;
            cursor: pointer;
            letter-spacing: .015em;
            transition: background 180ms, box-shadow 180ms, transform 100ms;
        }

        .login-submit:hover:not(:disabled) {
            background: var(--login-blue-mid);
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
        }

        .login-submit:active:not(:disabled) { transform: translateY(1px); }

        .login-submit:disabled { opacity: .7; cursor: not-allowed; }

        .login-register {
            margin-top: 1.1rem;
            font-size: .8125rem;
            color: var(--login-muted);
            text-align: center;
        }

        .login-status {
            margin-bottom: .75rem;
        }

        /* ============================================================
           PANEL KANAN — ILUSTRASI
           ============================================================ */
        .login-brand-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(145deg, var(--panel-from) 0%, var(--panel-mid) 50%, var(--panel-to) 100%);
            border-radius: 2.5rem 0 0 2.5rem;
        }

        .login-brand-panel::after {
            position: absolute;
            inset: 0;
            z-index: 1;
            content: '';
            background: linear-gradient(120deg, rgba(15,23,42,.22) 0%, transparent 40%, rgba(15,23,42,.1) 100%);
            pointer-events: none;
        }

        .login-brand-kinetic {
            position: absolute;
            inset: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* ---- DAUN DEKORATIF ---- */
        .login-leaves {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
        }

        .login-leaf-tr {
            position: absolute;
            top: -2%;
            right: -3%;
            width: clamp(6rem, 20%, 12rem);
            opacity: .7;
        }

        .login-leaf-br {
            position: absolute;
            bottom: -2%;
            right: -3%;
            width: clamp(5rem, 17%, 10rem);
            opacity: .6;
            transform: rotate(180deg);
        }

        .login-leaf-tl {
            position: absolute;
            top: -3%;
            left: -2%;
            width: clamp(4rem, 12%, 7rem);
            opacity: .45;
            transform: rotate(140deg) scaleX(-1);
        }

        /* ---- KOTAK KOMPOSISI ILUSTRASI ---- */
        .login-visual {
            position: absolute;
            z-index: 3;
            top: 10%;
            bottom: 5%;
            left: 50%;
            width: min(90%, 48rem);
            transform: translateX(-50%);
        }

        .login-visual::after {
            position: absolute;
            z-index: 0;
            inset: -10%;
            content: '';
            background: radial-gradient(circle, rgba(129,140,248,.55) 0%, rgba(99,102,241,.2) 40%, transparent 72%);
            filter: blur(40px);
            pointer-events: none;
        }

        /* ---- LINGKARAN BOLA BESAR ---- */
        .login-sphere {
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 50%;
            width: min(80%, 420px);
            aspect-ratio: 1;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            /* Cahaya lembut: tepi menghilang, tidak ada garis lingkaran keras */
            background:
                radial-gradient(circle at 35% 32%,
                    rgba(255,255,255,.22) 0%,
                    rgba(160,180,255,.10) 30%,
                    transparent          52%
                ),
                radial-gradient(circle at 55% 50%,
                    rgba(130,150,255,.55) 0%,
                    rgba(90,110,250,.38) 40%,
                    rgba(70,90,225,.18) 68%,
                    transparent         100%
                );
            box-shadow: 0 0 90px 30px rgba(80,102,255,.22);
            overflow: hidden;
            animation: sphere-breathe 8s ease-in-out infinite;
        }

        .login-sphere::before {
            position: absolute;
            content: '';
            top: 8%;
            left: 12%;
            width: 38%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,.22) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-sphere::after {
            position: absolute;
            content: '';
            bottom: 6%;
            right: 8%;
            width: 28%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(160,180,255,.15) 0%, transparent 70%);
            pointer-events: none;
        }

        @keyframes sphere-breathe {
            0%, 100% { opacity: .85; }
            50%       { opacity: 1; }
        }

        .login-visual-halo {
            position: absolute;
            z-index: 0;
            top: 50%;
            left: 50%;
            width: 92%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(199,210,254,.4), rgba(129,140,248,.15) 48%, transparent 70%);
            filter: blur(22px);
            transform: translate(-50%, -50%);
            animation: halo-breathe 9s ease-in-out infinite;
        }

        @keyframes halo-breathe {
            0%, 100% { opacity: .65; }
            50%       { opacity: 1; }
        }

        /* ---- GARIS KONEKTOR ---- */
        .login-connectors {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .connector-line {
            fill: none;
            stroke: rgba(255,255,255,.55);
            stroke-width: 0.4;
            stroke-linecap: round;
            filter: url(#login-connector-glow);
            opacity: .85;
        }

        /* ---- PARALLAX WRAPPER ----
           transform membuat stacking context sendiri, jadi z-index wajib
           eksplisit: tanpa ini lapisan ilustrasi dilukis di bawah
           .login-sphere (z-index 1) dan ikut tertutup. */
        .login-parallax {
            position: absolute;
            z-index: 2;
            inset: 0;
            pointer-events: none;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
            transition: transform 800ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        /* ---- KARAKTER ---- */
        .login-character {
            position: absolute;
            z-index: 4;
            top: 46%;
            left: 50%;
            width: 34%;
            transform: translate(-50%, -50%);
            animation: character-float 7.5s ease-in-out infinite;
        }

        .login-character img:first-child {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(10,20,80,.45));
        }

        .login-hand-icon {
            position: absolute;
            z-index: 5;
            top: 8%;
            left: 22%;
            width: 24%;
            transform: translate(-50%, -50%);
            filter: drop-shadow(0 0 10px rgba(186,230,253,.95)) drop-shadow(0 8px 14px rgba(15,23,42,.3));
            animation: hand-icon-float 6s ease-in-out infinite;
        }

        /* ---- IKON ORBIT ---- */
        .login-icon {
            position: absolute;
            z-index: 4;
            display: grid;
            width: clamp(2.6rem, 12%, 5.5rem);
            aspect-ratio: 1;
            place-items: center;
            transform: translate(-50%, -50%);
            animation: icon-float var(--float-duration, 6s) ease-in-out var(--float-delay, 0s) infinite;
        }

        .login-icon::before {
            position: absolute;
            inset: 0;
            z-index: -1;
            content: '';
            border-radius: 50%;
            background: radial-gradient(circle, var(--icon-glow, rgba(165,180,252,.85)), transparent 72%);
            filter: blur(12px);
            opacity: .9;
            animation: icon-pulse 4s ease-in-out var(--float-delay, 0s) infinite;
        }

        .login-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 6px rgba(224,231,255,.8)) drop-shadow(0 8px 14px rgba(15,23,42,.3));
        }

        .login-icon-2 { top: 40%; left: 22%; --icon-glow: rgba(96,165,250,.95);  --float-duration: 7.2s; --float-delay: -3.4s; }
        .login-icon-3 { top: 78%; left: 22%; --icon-glow: rgba(249,115,22,.92);  --float-duration: 5.8s; --float-delay: -2.3s; }
        .login-icon-4 { top: 26%; left: 70%; --icon-glow: rgba(239,68,68,.92);   --float-duration: 6.8s; --float-delay: -4.1s; }
        .login-icon-5 { top: 42%; left: 81%; --icon-glow: rgba(250,204,21,.95);  --float-duration: 7.6s; --float-delay: -1.8s; }
        .login-icon-6 { top: 74%; left: 48%; --icon-glow: rgba(251,146,60,.95);  --float-duration: 6.1s; --float-delay: -4.8s; }

        /* ---- ANIMASI ---- */
        @keyframes character-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-1deg); }
            50%       { transform: translate(-50%, calc(-50% - 0.9rem)) rotate(1deg); }
        }

        @keyframes hand-icon-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-3deg); }
            50%       { transform: translate(-50%, calc(-50% - 0.5rem)) rotate(4deg); }
        }

        @keyframes icon-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-2deg); }
            50%       { transform: translate(-50%, calc(-50% - 0.6rem)) rotate(3deg); }
        }

        @keyframes icon-pulse {
            0%, 100% { opacity: .55; }
            50%       { opacity: 1; }
        }

        @keyframes login-fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ---- TAGLINE ---- */
        .login-brand-content {
            position: absolute;
            z-index: 5;
            top: clamp(1.5rem, 5%, 2.75rem);
            left: 50%;
            width: min(72%, 20rem);
            transform: translateX(-50%);
            color: #ffffff;
            text-align: center;
        }

        .login-brand-content h2 {
            font-size: clamp(.9rem, 1.8vw, 1.1rem);
            font-weight: 700;
            line-height: 1.3;
        }

        .login-brand-content p {
            font-size: .78rem;
            opacity: .75;
            margin-top: .25rem;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 1023px) {
            .login-shell {
                display: flex;
                flex-direction: column;
                min-height: 100dvh;
            }

            .login-form-panel {
                order: 2;
                padding: 2rem clamp(1.25rem, 7vw, 3rem) 2.25rem;
            }

            .login-brand-panel {
                order: 1;
                min-height: clamp(13rem, 46vw, 17rem);
                border-radius: 0 0 2.5rem 2.5rem;
            }

            .login-brand-content { display: none; }

            .login-visual {
                top: 5%;
                bottom: 5%;
                width: min(90%, 22rem);
            }

            .login-character { width: 36%; }

            .login-sphere { width: min(82%, 280px); }
        }

        @media (max-width: 420px) {
            .login-brand-panel { min-height: 13rem; }
            .login-header { margin-top: 2.5rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-character,
            .login-icon,
            .login-icon::before,
            .login-hand-icon,
            .login-visual-halo,
            .login-sphere,
            .login-form-inner { animation: none !important; }

            .login-parallax { transition: none; }
        }
    </style>
</head>
<body>
    <main
        class="login-page"
        x-data="{
            raf: null,
            reducedMotion: false,
            compact: false,
            init() {
                this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                this.compact = window.matchMedia('(pointer: coarse), (max-width: 1023px)').matches;
            },
            move(event) {
                if (this.reducedMotion || this.compact) return;
                const x = (event.clientX / window.innerWidth  - 0.5) * 2;
                const y = (event.clientY / window.innerHeight - 0.5) * 2;
                cancelAnimationFrame(this.raf);
                this.raf = requestAnimationFrame(() => {
                    [
                        [this.$refs.connectors,  -4,  -3],
                        [this.$refs.character,   -8,  -6],
                        [this.$refs.icons,       12,   9],
                    ].forEach(([el, fx, fy]) => {
                        if (!el) return;
                        el.style.setProperty('--parallax-x', (x * fx) + 'px');
                        el.style.setProperty('--parallax-y', (y * fy) + 'px');
                    });
                });
            },
            reset() {
                if (this.reducedMotion || this.compact) return;
                cancelAnimationFrame(this.raf);
                [this.$refs.connectors, this.$refs.character, this.$refs.icons].forEach(el => {
                    if (!el) return;
                    el.style.setProperty('--parallax-x', '0px');
                    el.style.setProperty('--parallax-y', '0px');
                });
            }
        }"
    >
        <div class="login-shell">

            {{-- ================================================
                 PANEL KIRI — FORM
                 ================================================ --}}
            <section class="login-form-panel" aria-labelledby="login-title">
                <div class="login-form-inner">

                    {{-- Brand / Logo --}}
                    <div class="login-brand">
                        <div x-data="{ logoFailed: false }" class="login-brand-mark">
                            <img
                                x-show="!logoFailed"
                                x-on:error="logoFailed = true"
                                src="{{ asset('images/logo/logo-lightmode.png') }}"
                                alt="Tridaya App"
                            >
                            <span x-show="logoFailed" x-cloak class="login-brand-fallback">T</span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight text-gray-900">Tridaya App</span>
                    </div>

                    <div class="login-header">
                        <h1 id="login-title">Sign In to your Account</h1>
                        <p>Access your workspace and keep your work connected.</p>
                    </div>

                    <x-auth-session-status
                        class="login-status rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
                        :status="session('status')"
                    />

                    <form
                        method="POST"
                        action="{{ route('login') }}"
                        x-data="{ isSubmitting: false }"
                        x-on:submit="isSubmitting = true"
                        class="login-form"
                    >
                        @csrf

                        <div class="login-field">
                            <label for="email" class="login-label-row">
                                <span class="login-label">Email</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter Email"
                                required
                                autofocus
                                autocomplete="username"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                class="login-input"
                            >
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <div x-data="{ showPassword: false, capsLock: false }" class="login-field">
                            <div class="login-label-row">
                                <label for="password" class="login-label">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="login-link">Forgot password?</a>
                                @endif
                            </div>
                            <div class="login-input-wrap">
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    placeholder="Enter Password"
                                    required
                                    autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    x-on:keyup="capsLock = $event.getModifierState('CapsLock')"
                                    x-on:keydown="capsLock = $event.getModifierState('CapsLock')"
                                    class="login-input login-input-password"
                                >
                                <button
                                    type="button"
                                    x-on:click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                    :aria-pressed="showPassword"
                                    aria-controls="password"
                                    class="login-password-toggle"
                                >
                                    <svg x-show="!showPassword" class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p x-show="capsLock" x-cloak role="alert" class="mt-2 rounded-md bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">
                                Caps Lock is on
                            </p>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <label for="remember" class="login-remember">
                            <input id="remember" type="checkbox" name="remember">
                            Remember me
                        </label>

                        <button
                            type="submit"
                            x-bind:disabled="isSubmitting"
                            x-bind:aria-busy="isSubmitting"
                            class="login-submit"
                        >
                            <span x-show="!isSubmitting">SIGN IN</span>
                            <span x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Signing in...
                            </span>
                        </button>
                    </form>

                    @if (Route::has('register'))
                        <p class="login-register">
                            Not registered yet?
                            <a href="{{ route('register') }}" class="login-link">Create an account</a>
                        </p>
                    @endif

                </div>
            </section>

            {{-- ================================================
                 PANEL KANAN — ILUSTRASI
                 ================================================ --}}
            <aside
                class="login-brand-panel"
                aria-label="Tridaya App"
                x-on:pointermove="move($event)"
                x-on:pointerleave="reset()"
            >
                <canvas id="login-kinetic" class="login-brand-kinetic" aria-hidden="true"></canvas>

                {{-- Daun dekoratif --}}
                <div class="login-leaves" aria-hidden="true">
                    <svg class="login-leaf-tr" viewBox="0 0 120 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M60 190 C20 160, 5 120, 10 70 C15 30, 50 10, 60 10 C70 10, 105 30, 110 70 C115 120, 100 160, 60 190Z" fill="rgba(67,90,200,0.45)" />
                        <path d="M60 10 C60 10, 60 100, 60 190" stroke="rgba(130,150,255,0.35)" stroke-width="2"/>
                        <path d="M60 50 C45 60, 30 80, 20 100" stroke="rgba(130,150,255,0.25)" stroke-width="1.5"/>
                        <path d="M60 50 C75 60, 90 80, 100 100" stroke="rgba(130,150,255,0.25)" stroke-width="1.5"/>
                        <path d="M60 90 C48 100, 35 115, 25 135" stroke="rgba(130,150,255,0.2)" stroke-width="1.5"/>
                        <path d="M60 90 C72 100, 85 115, 95 135" stroke="rgba(130,150,255,0.2)" stroke-width="1.5"/>
                    </svg>

                    <svg class="login-leaf-br" viewBox="0 0 120 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M60 190 C20 160, 5 120, 10 70 C15 30, 50 10, 60 10 C70 10, 105 30, 110 70 C115 120, 100 160, 60 190Z" fill="rgba(67,90,200,0.4)" />
                        <path d="M60 10 C60 10, 60 100, 60 190" stroke="rgba(130,150,255,0.3)" stroke-width="2"/>
                        <path d="M60 50 C45 60, 30 80, 20 100" stroke="rgba(130,150,255,0.22)" stroke-width="1.5"/>
                        <path d="M60 50 C75 60, 90 80, 100 100" stroke="rgba(130,150,255,0.22)" stroke-width="1.5"/>
                    </svg>

                    <svg class="login-leaf-tl" viewBox="0 0 100 170" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M50 160 C18 135, 5 100, 8 60 C11 25, 38 8, 50 8 C62 8, 89 25, 92 60 C95 100, 82 135, 50 160Z" fill="rgba(55,80,190,0.35)" />
                        <path d="M50 8 C50 8, 50 85, 50 160" stroke="rgba(120,140,240,0.3)" stroke-width="1.5"/>
                    </svg>
                </div>

                <div class="login-brand-content">
                    <h2>Connecting People, Empowering Business</h2>
                    <p>Technology and communication that keeps your team connected.</p>
                </div>

                <div class="login-visual" aria-hidden="true">
                    <div class="login-visual-halo"></div>

                    {{-- Lingkaran bola besar --}}
                    <div class="login-sphere"></div>

                    <div x-ref="connectors" class="login-parallax">
                        <svg
                            class="login-connectors"
                            viewBox="0 0 100 100"
                            preserveAspectRatio="none"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <defs>
                                <filter id="login-connector-glow" x="-80%" y="-80%" width="260%" height="260%">
                                    <feGaussianBlur stdDeviation="1.8" result="blur" />
                                    <feMerge>
                                        <feMergeNode in="blur" />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>
                            </defs>

                            <path class="connector-line" d="M52 50C50 44 45 33 40.5 26" />
                            <path class="connector-line" d="M52 50C44 46 33 43 22 40" />
                            <path class="connector-line" d="M52 50C41 64 33 71 22 78" />
                            <path class="connector-line" d="M52 50C61 40 66 33 70 26" />
                            <path class="connector-line" d="M52 50C64 47 72 45 81 42" />
                            <path class="connector-line" d="M52 50C48 61 48 67 48 74" />

                            <circle cx="40.5" cy="26" r="1.3" fill="#BAE6FD" />
                            <circle cx="22" cy="40" r="1.2" fill="#93C5FD" />
                            <circle cx="22" cy="78" r="1.2" fill="#FB923C" />
                            <circle cx="70" cy="26" r="1.2" fill="#F87171" />
                            <circle cx="81" cy="42" r="1.2" fill="#FDE047" />
                            <circle cx="48" cy="74" r="1.2" fill="#FDBA74" />
                        </svg>
                    </div>

                    <div x-ref="character" class="login-parallax">
                        <div class="login-character">
                            <img
                                src="{{ asset('images/Asset_LoginPage/character2.svg') }}"
                                alt=""
                                fetchpriority="high"
                                decoding="async"
                            >
                            <img
                                class="login-hand-icon"
                                src="{{ asset('images/Asset_LoginPage/icon1.svg') }}"
                                alt=""
                                decoding="async"
                            >
                        </div>
                    </div>

                    <div x-ref="icons" class="login-parallax">
                        <div class="login-icon login-icon-2">
                            <img src="{{ asset('images/Asset_LoginPage/icon2.svg') }}" alt="" decoding="async">
                        </div>
                        <div class="login-icon login-icon-3">
                            <img src="{{ asset('images/Asset_LoginPage/icon3.svg') }}" alt="" decoding="async">
                        </div>
                        <div class="login-icon login-icon-4">
                            <img src="{{ asset('images/Asset_LoginPage/icon4.svg') }}" alt="" decoding="async">
                        </div>
                        <div class="login-icon login-icon-5">
                            <img src="{{ asset('images/Asset_LoginPage/icon5.svg') }}" alt="" decoding="async">
                        </div>
                        <div class="login-icon login-icon-6">
                            <img src="{{ asset('images/Asset_LoginPage/icon6.svg') }}" alt="" decoding="async">
                        </div>
                    </div>
                </div>

            </aside>

        </div>
    </main>
</body>
</html>
