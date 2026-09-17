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

        :root {
            --login-blue: #2563eb;
            --login-ink: #111827;
            --login-muted: #6b7280;
            --login-border: #d1d5db;
            --login-paper: #f8fafc;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            background: var(--login-paper);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            color: var(--login-ink);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* ============================================================
           Halaman: full-bleed form + panel ilustrasi
           ============================================================ */
        .login-page {
            min-height: 100vh;
            min-height: 100dvh;
            background: #ffffff;
        }

        .login-shell {
            display: grid;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            grid-template-columns: 45% 55%;
            overflow: hidden;
            background: #ffffff;
        }

        /* ============================================================
           Panel kiri: form
           ============================================================ */
        .login-form-panel {
            position: relative;
            display: flex;
            min-width: 0;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            background: #ffffff;
            padding: clamp(2rem, 5vw, 4.5rem) clamp(1.5rem, 5vw, 4rem);
        }

        .login-form-panel::before {
            position: absolute;
            z-index: 0;
            inset: 0;
            background-image:
                radial-gradient(circle at 12% 18%, rgba(99, 102, 241, 0.12) 0 1px, transparent 1.5px),
                radial-gradient(circle at 82% 76%, rgba(236, 72, 153, 0.09) 0 1px, transparent 1.5px),
                linear-gradient(135deg, rgba(224, 231, 255, 0.24), transparent 34%, rgba(251, 207, 232, 0.14));
            background-size: 32px 32px, 46px 46px, auto;
            content: '';
            opacity: 0.48;
            pointer-events: none;
        }

        .login-form-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 24rem);
            margin: 0 auto;
        }

        .login-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
        }

        .login-brand-mark {
            display: inline-flex;
            width: 2.25rem;
            height: 2.25rem;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0.65rem;
            background: #eff6ff;
        }

        .login-brand-mark img {
            width: 1.65rem;
            height: 1.65rem;
            object-fit: contain;
        }

        .login-brand-fallback {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 800;
            background: var(--login-blue);
        }

        .login-header {
            margin-top: clamp(2.5rem, 7vh, 4.5rem);
        }

        .login-header h1 {
            margin: 0;
            color: var(--login-ink);
            font-size: clamp(1.5rem, 2.4vw, 1.75rem);
            font-weight: 600;
            letter-spacing: -0.035em;
            line-height: 1.25;
        }

        .login-header p {
            margin: 0.55rem 0 0;
            color: var(--login-muted);
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .login-form {
            margin-top: 2rem;
        }

        .login-field + .login-field {
            margin-top: 1.2rem;
        }

        .login-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.45rem;
        }

        .login-label {
            color: #374151;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .login-input-wrap {
            position: relative;
        }

        .login-input {
            display: block;
            width: 100%;
            min-height: 3rem;
            border: 1px solid var(--login-border);
            border-radius: 0.55rem;
            background: #ffffff;
            color: var(--login-ink);
            font-size: 0.82rem;
            outline: 0;
            padding: 0.7rem 0.85rem;
            transition: border-color 200ms ease, box-shadow 200ms ease;
        }

        .login-input::placeholder {
            color: #9ca3af;
        }

        .login-input:focus {
            border-color: var(--login-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.16);
        }

        .login-input-password {
            padding-right: 3.25rem;
        }

        .login-password-toggle {
            position: absolute;
            top: 50%;
            right: 0.25rem;
            display: inline-flex;
            width: 2.65rem;
            height: 2.65rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.45rem;
            color: #9ca3af;
            transform: translateY(-50%);
            transition: color 200ms ease, background-color 200ms ease;
        }

        .login-password-toggle:hover {
            color: #4b5563;
            background: #f3f4f6;
        }

        .login-password-toggle:focus-visible,
        .login-link:focus-visible,
        .login-submit:focus-visible {
            outline: 2px solid var(--login-blue);
            outline-offset: 2px;
        }

        .login-link {
            color: #2563eb;
            font-size: 0.72rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 200ms ease;
        }

        .login-link:hover {
            color: #1d4ed8;
        }

        .login-remember {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
            color: #4b5563;
            cursor: pointer;
            font-size: 0.78rem;
            user-select: none;
        }

        .login-remember input {
            width: 1rem;
            height: 1rem;
            accent-color: var(--login-blue);
        }

        .login-submit {
            display: inline-flex;
            width: 100%;
            min-height: 3rem;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            border: 0;
            border-radius: 0.55rem;
            background: #2563eb;
            box-shadow: 0 5px 12px -8px rgba(37, 99, 235, 0.75);
            color: #ffffff;
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 600;
            transition: background-color 200ms ease, box-shadow 200ms ease, transform 200ms ease;
        }

        .login-submit:hover:not(:disabled) {
            background: #1d4ed8;
            box-shadow: 0 8px 16px -9px rgba(37, 99, 235, 0.85);
            transform: translateY(-1px);
        }

        .login-submit:active:not(:disabled) {
            background: #1e40af;
            transform: translateY(0);
        }

        .login-submit:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .login-register {
            margin: 2rem 0 0;
            color: var(--login-muted);
            font-size: 0.78rem;
            line-height: 1.5;
            text-align: center;
        }

        .login-status {
            margin-top: 1.25rem;
        }

        /* ============================================================
           Panel kanan: navy + kinetic grid + ilustrasi
           ============================================================ */
        .login-brand-panel {
            position: relative;
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(150deg, #221a8f 0%, #2f2ac4 46%, #4f46e5 100%);
            border-radius: 28% 0 0 28% / 50% 0 0 50%;
        }

        .login-brand-kinetic {
            position: absolute;
            z-index: 0;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .login-brand-panel::after {
            position: absolute;
            z-index: 1;
            inset: 0;
            background: linear-gradient(120deg, rgba(15, 23, 42, 0.3) 0%, transparent 38%, rgba(15, 23, 42, 0.14) 100%);
            content: '';
            pointer-events: none;
        }

        .login-brand-content {
            position: absolute;
            z-index: 3;
            top: clamp(1.75rem, 5.5%, 3rem);
            left: 50%;
            width: min(70%, 21rem);
            transform: translateX(-50%);
            color: #ffffff;
            text-align: center;
        }

        .login-brand-content h2 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(1.15rem, 1.8vw, 1.5rem);
            font-weight: 600;
            letter-spacing: -0.035em;
            line-height: 1.3;
        }

        .login-brand-content p {
            margin: 0.5rem 0 0;
            color: #c7d2fe;
            font-size: 0.78rem;
            line-height: 1.6;
        }

        /* ---- Composition ---- */
        .login-visual {
            position: absolute;
            z-index: 2;
            top: 22%;
            bottom: 3%;
            left: 50%;
            width: min(86%, 46rem);
            transform: translateX(-50%);
        }

        .login-visual-halo {
            position: absolute;
            z-index: 0;
            top: 46%;
            left: 50%;
            width: 78%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(199, 210, 254, 0.42), rgba(129, 140, 248, 0.16) 45%, transparent 70%);
            filter: blur(10px);
            transform: translate(-50%, -50%);
            animation: halo-breathe 9s ease-in-out infinite;
        }

        .login-parallax {
            position: absolute;
            inset: 0;
            pointer-events: none;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
            transition: transform 800ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .login-connectors {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .connector-line {
            fill: none;
            stroke: #e0e7ff;
            stroke-width: 0.8;
            stroke-linecap: round;
            filter: url(#login-connector-glow);
            opacity: 0.75;
        }

        .login-character {
            position: absolute;
            z-index: 2;
            top: 52%;
            left: 52%;
            width: 31%;
            transform: translate(-50%, -50%);
            animation: character-float 7.5s ease-in-out infinite;
        }

        .login-character img {
            display: block;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 16px 18px rgba(15, 23, 42, 0.34));
        }

        .login-hand-icon {
            position: absolute;
            z-index: 3;
            top: 40%;
            left: 55%;
            width: 24%;
            transform: translate(-50%, -50%);
            filter: drop-shadow(0 0 6px rgba(186, 230, 253, 0.85)) drop-shadow(0 8px 12px rgba(15, 23, 42, 0.3));
            animation: hand-icon-float 6s ease-in-out infinite;
        }

        .login-icon {
            position: absolute;
            z-index: 3;
            display: grid;
            width: clamp(2.6rem, 13%, 6rem);
            aspect-ratio: 1;
            place-items: center;
            animation: icon-float var(--float-duration, 6s) ease-in-out var(--float-delay, 0s) infinite;
        }

        .login-icon::before {
            position: absolute;
            z-index: -1;
            inset: 6%;
            border-radius: 50%;
            background: radial-gradient(circle, var(--icon-glow, rgba(165, 180, 252, 0.85)), transparent 70%);
            filter: blur(10px);
            opacity: 0.9;
            content: '';
            animation: icon-pulse 4s ease-in-out var(--float-delay, 0s) infinite;
        }

        .login-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 5px rgba(224, 231, 255, 0.72)) drop-shadow(0 8px 12px rgba(15, 23, 42, 0.32));
        }

        .login-icon-2 { top: 37%; left: 1%; --icon-glow: rgba(196, 181, 253, 0.95); --float-duration: 7.2s; --float-delay: -3.4s; }
        .login-icon-3 { top: 73%; left: 17%; --icon-glow: rgba(249, 168, 212, 0.92); --float-duration: 5.8s; --float-delay: -2.3s; }
        .login-icon-4 { top: 1%; left: 72%; --icon-glow: rgba(253, 230, 138, 0.95); --float-duration: 6.8s; --float-delay: -4.1s; }
        .login-icon-5 { top: 38%; left: 85%; --icon-glow: rgba(253, 186, 116, 0.92); --float-duration: 7.6s; --float-delay: -1.8s; }
        .login-icon-6 { top: 78%; left: 50%; --icon-glow: rgba(252, 165, 165, 0.95); --float-duration: 6.1s; --float-delay: -4.8s; }

        @keyframes character-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-1deg); }
            50% { transform: translate(-50%, calc(-50% - 0.85rem)) rotate(1deg); }
        }

        @keyframes icon-float {
            0%, 100% { transform: translate3d(0, 0, 0) rotate(-2deg); }
            50% { transform: translate3d(0, -0.6rem, 0) rotate(3deg); }
        }

        @keyframes icon-pulse {
            0%, 100% { opacity: 0.55; }
            50% { opacity: 1; }
        }

        @keyframes halo-breathe {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        @keyframes hand-icon-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-3deg); }
            50% { transform: translate(-50%, calc(-50% - 0.5rem)) rotate(4deg); }
        }

        @media (prefers-reduced-motion: no-preference) {
            .login-form-inner {
                animation: login-fade-in 500ms ease both;
            }
        }

        @keyframes login-fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ============================================================
           Responsive
           ============================================================ */
        @media (max-width: 1023px) {
            .login-page {
                padding: 0;
            }

            .login-shell {
                display: flex;
                min-height: 100dvh;
                flex-direction: column;
                border-radius: 0;
            }

            .login-form-panel {
                order: 2;
                padding: 2rem clamp(1.25rem, 7vw, 3rem) 2.25rem;
            }

            .login-brand-panel {
                order: 1;
                height: clamp(13rem, 46vw, 17rem);
                border-radius: 0 0 2.5rem 2.5rem;
            }

            .login-brand-content {
                display: none;
            }

            .login-visual {
                top: 8%;
                bottom: 8%;
                left: 50%;
                width: min(88%, 22rem);
                transform: translateX(-50%);
            }

            .login-character {
                width: 34%;
            }
        }

        @media (max-width: 420px) {
            .login-brand-panel {
                height: 12.5rem;
            }

            .login-header {
                margin-top: 2.75rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-character,
            .login-icon,
            .login-icon::before,
            .login-hand-icon,
            .login-visual-halo,
            .login-form-inner {
                animation: none !important;
            }

            .login-parallax {
                transition: none;
            }
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
                const x = (event.clientX / window.innerWidth - 0.5) * 2;
                const y = (event.clientY / window.innerHeight - 0.5) * 2;
                cancelAnimationFrame(this.raf);
                this.raf = requestAnimationFrame(() => {
                    [[this.$refs.connectors, -4, -3], [this.$refs.character, -8, -6], [this.$refs.icons, 12, 9]]
                        .forEach(([element, fx, fy]) => {
                            if (!element) return;
                            element.style.setProperty('--parallax-x', (x * fx) + 'px');
                            element.style.setProperty('--parallax-y', (y * fy) + 'px');
                        });
                });
            },
            reset() {
                if (this.reducedMotion || this.compact) return;
                cancelAnimationFrame(this.raf);
                [this.$refs.connectors, this.$refs.character, this.$refs.icons].forEach((element) => {
                    if (!element) return;
                    element.style.setProperty('--parallax-x', '0px');
                    element.style.setProperty('--parallax-y', '0px');
                });
            }
        }"
    >
        <div class="login-shell">
            <section class="login-form-panel" aria-labelledby="login-title">
                <div class="login-form-inner">
                    <div class="login-brand">
                        <div x-data="{ logoFailed: false }" class="login-brand-mark">
                            <img
                                x-show="!logoFailed"
                                x-on:error="logoFailed = true"
                                src="{{ asset('images/logo/logo-lightmode.png') }}"
                                alt="Tridaya App"
                            >
                            <span x-show="logoFailed" x-cloak class="login-brand-mark login-brand-fallback">T</span>
                        </div>
                        <span class="text-xl font-semibold tracking-tight text-gray-900">Tridaya App</span>
                    </div>

                    <div class="login-header">
                        <h1 id="login-title">Sign in to your account</h1>
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
                                <span class="login-label">Email address</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@company.com"
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
                                    placeholder="Enter your password"
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
                            <p x-show="capsLock" x-cloak role="alert" class="mt-2 rounded-md bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">Caps Lock is on</p>
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
                            <span x-show="!isSubmitting">Sign In</span>
                            <span x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Signing in...
                            </span>
                            <svg x-show="!isSubmitting" class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
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

            <aside
                class="login-brand-panel"
                aria-label="Tridaya App"
                x-on:pointermove="move($event)"
                x-on:pointerleave="reset()"
            >
                <canvas id="login-kinetic" class="login-brand-kinetic" aria-hidden="true"></canvas>

                <div class="login-brand-content">
                    <h2>Connecting People, Empowering Business</h2>
                    <p>Technology and communication that keeps your team connected.</p>
                </div>

                <div class="login-visual" aria-hidden="true">
                    <div class="login-visual-halo"></div>

                    <div x-ref="connectors" class="login-parallax">
                        <svg class="login-connectors" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <filter id="login-connector-glow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur stdDeviation="1.1" result="blur" />
                                    <feMerge>
                                        <feMergeNode in="blur" />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>
                            </defs>

                            <path class="connector-line" d="M52 52C44 42 35 18 27.5 7.5" />
                            <path class="connector-line" d="M52 52C38 49 20 44 7.5 43.5" />
                            <path class="connector-line" d="M52 54C43 64 32 76 23.5 79.5" />
                            <path class="connector-line" d="M54 50C62 39 72 17 78.5 7.5" />
                            <path class="connector-line" d="M56 52C68 50 82 46 91.5 44.5" />
                            <path class="connector-line" d="M54 56C55 66 56 77 56.5 84.5" />

                            <circle cx="27.5" cy="7.5" r="1.1" fill="#BAE6FD" />
                            <circle cx="7.5" cy="43.5" r="1.1" fill="#C4B5FD" />
                            <circle cx="23.5" cy="79.5" r="1.1" fill="#F9A8D4" />
                            <circle cx="78.5" cy="7.5" r="1.1" fill="#FDE68A" />
                            <circle cx="91.5" cy="44.5" r="1.1" fill="#FDBA74" />
                            <circle cx="56.5" cy="84.5" r="1.1" fill="#FCA5A5" />
                        </svg>
                    </div>

                    <div x-ref="character" class="login-parallax">
                        <div class="login-character">
                            <img src="{{ asset('images/Asset_LoginPage/character2.svg') }}" alt="" fetchpriority="high" decoding="async">
                            <img class="login-hand-icon" src="{{ asset('images/Asset_LoginPage/icon1.svg') }}" alt="" decoding="async">
                        </div>
                    </div>

                    <div x-ref="icons" class="login-parallax">
                        <div class="login-icon login-icon-2"><img src="{{ asset('images/Asset_LoginPage/icon2.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-3"><img src="{{ asset('images/Asset_LoginPage/icon3.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-4"><img src="{{ asset('images/Asset_LoginPage/icon4.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-5"><img src="{{ asset('images/Asset_LoginPage/icon5.svg') }}" alt="" decoding="async"></div>
                        <div class="login-icon login-icon-6"><img src="{{ asset('images/Asset_LoginPage/icon6.svg') }}" alt="" decoding="async"></div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
