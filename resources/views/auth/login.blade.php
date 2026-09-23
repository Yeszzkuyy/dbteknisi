<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">

    <script>
        (() => {
            let mode = 'system';
            try { mode = localStorage.getItem('appearance-mode') || 'system'; } catch (error) {}
            const dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --bg: #f1f5f9;
            --card-bg: #fff;
            --input-border: #cbd5e1;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --nav-hover-bg: rgba(15, 23, 42, .05);
            --accent-500: 59 130 246;
            --accent-600: 37 99 235;
            --accent-700: 29 78 216;
            --accent-800: 30 64 175;
            --accent-900: 30 58 138;
            --accent-950: 23 37 84;
            --semantic-success: 34 197 94;
            --semantic-danger: 239 68 68;
            --login-panel: rgb(var(--accent-800) / 1);
            --login-accent: rgb(var(--accent-600) / 1);
            --login-accent-hover: rgb(var(--accent-700) / 1);
            --login-surface: var(--bg);
            --login-field: var(--card-bg);
            --login-border: var(--input-border);
            --login-text: var(--text-primary);
            --login-muted: var(--text-secondary);
            --login-placeholder: var(--text-muted);
            --login-focus: rgb(var(--accent-500) / .28);
            --aurora-base: #f0f7ff;
            --aurora-1: #bfdbfe;
            --aurora-2: #7dd3fc;
            --aurora-3: #a5f3fc;
            --aurora-4: #1a56db;
            --aurora-glow: #0ea5e9;
        }

        .dark {
            --aurora-base: #0f1623;
            --aurora-1: #0d2b5e;
            --aurora-2: #0e4d6e;
            --aurora-3: #0a7a6e;
            --aurora-4: #1a56db;
            --aurora-glow: #00d4ff;
            --login-surface: #252b40;
            --login-field: #1e2438;
            --login-border: #3d4870;
            --login-text: #e2e8f8;
            --login-muted: #8d96b8;
            --login-placeholder: #545d7e;
            --login-accent: #3b82f6;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body { min-height: 100%; margin: 0; }

        body {
            display: grid;
            min-height: 100dvh;
            padding: 2rem 1.25rem;
            place-items: center;
            background: var(--aurora-base);
            color: var(--login-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        body::before {
            position: fixed;
            inset: -5%;
            z-index: 0;
            background:
                radial-gradient(ellipse 45% 35% at 15% 20%, var(--aurora-1), transparent 70%),
                radial-gradient(ellipse 50% 40% at 85% 15%, var(--aurora-2), transparent 70%),
                radial-gradient(ellipse 55% 45% at 50% 90%, var(--aurora-3), transparent 70%),
                radial-gradient(ellipse 60% 45% at 50% 42%, var(--aurora-4), transparent 70%);
            content: "";
            pointer-events: none;
            opacity: .6;
        }

        .dark body::before { opacity: .8; }

        .login-particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        @property --beam-angle {
            syntax: '<angle>';
            initial-value: 0deg;
            inherits: false;
        }

        @property --beam-angle-2 {
            syntax: '<angle>';
            initial-value: 180deg;
            inherits: false;
        }

        .login-page {
            position: relative;
            z-index: 1;
            display: grid;
            width: min(80rem, 100%);
            min-height: min(40rem, calc(100dvh - 4rem));
            overflow: hidden;
            grid-template-columns: minmax(0, 50%) minmax(0, 50%);
            border: 1.5px solid transparent;
            border-radius: 1.75rem;
            padding: clamp(.9rem, 2vw, 1.5rem);
            background:
                linear-gradient(var(--login-surface), var(--login-surface)) padding-box,
                conic-gradient(from var(--beam-angle, 0deg), transparent 0 55%, var(--aurora-glow) 70%, #fff 82%, transparent 95%) border-box,
                conic-gradient(from var(--beam-angle-2, 180deg), transparent 0 55%, var(--aurora-glow) 70%, #fff 82%, transparent 95%) border-box;
            box-shadow: 0 24px 80px -24px rgb(var(--accent-950) / .35), 0 4px 16px rgb(var(--accent-950) / .12), 0 0 70px -18px var(--aurora-glow);
        }

        .dark .login-page { box-shadow: 0 24px 80px -24px rgb(0 0 0 / .6); }

        .login-visual {
            position: relative;
            display: flex;
            min-height: 100%;
            order: 1;
            flex-direction: column;
            overflow: hidden;
            padding: clamp(2rem, 4vw, 3.5rem);
            border: 1px solid rgb(255 255 255 / .16);
            border-radius: 1.25rem;
            isolation: isolate;
            color: #f8fafc;
            background:
                linear-gradient(135deg, #2563eb 0%, rgb(var(--accent-700) / 1) 100%),
                var(--login-panel);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / .12);
        }

        .login-brand {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: .8rem;
        }

        .login-brand-mark {
            display: block;
            width: 4.1rem;
            height: 2.8rem;
            object-fit: contain;
            object-position: left center;
        }

        .login-brand-copy {
            display: flex;
            flex-direction: column;
            gap: .16rem;
        }

        .login-brand-name {
            color: #f8fafc;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -.025em;
            line-height: 1.1;
        }

        .login-brand-group {
            color: rgb(191 219 254 / .7);
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .1em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .login-visual h2 {
            max-width: 22rem;
            margin: clamp(2rem, 6vh, 4rem) 0 0;
            color: #f8fafc;
            font-size: clamp(1.6rem, 2.6vw, 2.4rem);
            font-weight: 700;
            letter-spacing: -.05em;
            line-height: 1.12;
        }

        .login-illustration {
            display: block;
            width: min(100%, 26rem);
            height: auto;
            margin: auto auto 0;
            padding-top: clamp(1.5rem, 4vh, 3rem);
            border-radius: 1.25rem;
            filter: drop-shadow(0 18px 32px rgb(2 6 23 / .35));
        }

        .login-form-panel {
            position: relative;
            display: flex;
            min-width: 0;
            order: 2;
            align-items: center;
            justify-content: center;
            padding: clamp(2.5rem, 5vw, 4.5rem) clamp(2rem, 5vw, 4rem);
            isolation: isolate;
            background: var(--login-surface);
        }

        .login-form-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 26rem);
        }

        .login-header { margin-bottom: 2.2rem; }

        .login-header h1 {
            margin: 0;
            color: var(--login-text);
            font-size: clamp(2.2rem, 4vw, 3rem);
            font-weight: 700;
            letter-spacing: -.05em;
            line-height: 1.12;
        }

        .login-header p {
            margin: .7rem 0 0;
            color: var(--login-muted);
            font-size: .95rem;
            line-height: 1.6;
        }

        .login-status {
            margin: 0 0 1.1rem;
            padding: .8rem .9rem;
            border: 1px solid rgb(var(--semantic-success) / .3);
            border-radius: .55rem;
            color: rgb(21 128 61 / 1);
            background: rgb(var(--semantic-success) / .08);
            font-size: .8rem;
            line-height: 1.5;
        }

        .dark .login-status { color: rgb(134 239 172 / 1); }

        .login-form { display: flex; flex-direction: column; gap: 1.2rem; }

        .login-field { display: flex; flex-direction: column; gap: .48rem; }

        .login-label-row,
        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .login-label {
            color: var(--login-text);
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .login-input-wrap { position: relative; }

        .login-input {
            display: block;
            width: 100%;
            min-height: 3.4rem;
            padding: .85rem 1rem;
            border: 1.5px solid var(--login-border);
            border-radius: .55rem;
            outline: none;
            color: var(--login-text);
            background: var(--login-field);
            font: inherit;
            font-size: 1rem;
            line-height: 1.5;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .login-input::placeholder { color: var(--login-placeholder); opacity: .9; }

        .login-input:-webkit-autofill,
        .login-input:-webkit-autofill:hover,
        .login-input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--login-text);
            caret-color: var(--login-text);
            transition: background-color 9999s ease-in-out 0s;
        }

        .login-input:hover {
            box-shadow: 0 0 14px rgb(var(--accent-500) / .18);
        }

        .login-form .login-input:focus {
            border-color: transparent;
            outline: none;
            --tw-ring-shadow: 0 0 #0000;
            --tw-ring-offset-shadow: 0 0 #0000;
            background:
                linear-gradient(var(--login-field), var(--login-field)) padding-box,
                conic-gradient(from var(--beam-angle, 0deg), #ff2d78, #ffb800, #00e5ff, #7c5cff, #ff2d78) border-box;
            box-shadow: 0 0 22px rgb(var(--accent-500) / .30);
        }

        .login-input[aria-invalid="true"] {
            border-color: rgb(var(--semantic-danger) / .8);
        }

        .login-input-password { padding-right: 4.4rem; }

        .login-password-toggle {
            position: absolute;
            top: 50%;
            right: .55rem;
            display: inline-flex;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: .4rem;
            color: var(--login-muted);
            background: transparent;
            cursor: pointer;
            font: inherit;
        }

        .login-password-toggle:hover { color: var(--login-text); background: var(--nav-hover-bg); }

        .login-link {
            color: var(--login-accent);
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
            text-underline-offset: 3px;
        }

        .login-link:hover { color: var(--login-accent-hover); text-decoration: underline; }

        .login-error {
            margin: 0;
            padding: 0;
            color: rgb(var(--semantic-danger) / 1);
            font-size: .8rem;
            line-height: 1.45;
            list-style: none;
        }

        .login-caps-lock {
            margin: 0;
            color: rgb(180 83 9 / 1);
            font-size: .75rem;
            line-height: 1.45;
        }

        .dark .login-caps-lock { color: rgb(253 186 116 / 1); }

        .login-remember {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: var(--login-muted);
            font-size: .82rem;
            cursor: pointer;
            user-select: none;
        }

        .login-remember input {
            width: 1rem;
            height: 1rem;
            margin: 0;
            accent-color: var(--login-accent);
            cursor: pointer;
        }

        .login-submit {
            display: inline-flex;
            width: 100%;
            min-height: 3.4rem;
            align-items: center;
            justify-content: center;
            padding: .85rem 1rem;
            border: 1px solid var(--login-accent);
            border-radius: .55rem;
            color: #f8fafc;
            background: var(--login-accent);
            cursor: pointer;
            font: inherit;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .01em;
            transition: background-color 180ms ease, border-color 180ms ease, transform 120ms ease, box-shadow 180ms ease;
        }

        .login-submit:hover:not(:disabled) {
            border-color: var(--login-accent-hover);
            background: var(--login-accent-hover);
            box-shadow: 0 7px 16px rgb(var(--accent-900) / .16);
        }

        .login-submit:active:not(:disabled) { transform: translateY(1px); }
        .login-submit:disabled { cursor: not-allowed; opacity: .7; }

        .login-register {
            margin: 1.35rem 0 0;
            color: var(--login-muted);
            font-size: .82rem;
            line-height: 1.5;
            text-align: center;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 3px solid var(--login-focus);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: no-preference) {
            @keyframes login-rise {
                from { opacity: 0; transform: translateY(14px); }
                to { opacity: 1; transform: none; }
            }

            @keyframes aurora-drift {
                from { transform: translate3d(-1.5%, -1%, 0) scale(1); }
                to { transform: translate3d(1.5%, 1%, 0) scale(1.04); }
            }

            body::before { animation: aurora-drift 26s ease-in-out infinite alternate; }

            @keyframes beam-spin {
                to { --beam-angle: 360deg; }
            }

            @keyframes beam-spin-chase {
                to { --beam-angle-2: 540deg; }
            }

            .login-page { animation: beam-spin 10s linear infinite, beam-spin-chase 16s linear infinite; }
            .login-form .login-input:focus { animation: beam-spin 6s linear infinite; }

            .login-brand { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) backwards; }
            .login-visual h2 { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) .08s backwards; }
            .login-illustration { animation: login-rise .6s cubic-bezier(.16, 1, .3, 1) .16s backwards; }
            .login-form-inner { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) .1s backwards; }
        }

        @media (max-width: 1023px) {
            .login-visual { padding: clamp(1.75rem, 4vw, 2.75rem); }
            .login-form-panel { padding: clamp(2rem, 5vw, 3.5rem) clamp(1.5rem, 5vw, 3rem); }
        }

        @media (max-width: 767px) {
            body { padding: 1.25rem .9rem; }

            .login-page { grid-template-columns: 1fr; border-radius: 1.25rem; min-height: 0; }
            .login-visual { min-height: 0; order: 2; padding: 1.75rem 1.5rem 1.5rem; }
            .login-visual h2 { margin-top: 1.4rem; font-size: 1.5rem; }
            .login-brand-mark { width: 3.5rem; height: 2.35rem; }
            .login-illustration { width: min(100%, 20rem); padding-top: 1.25rem; }
            .login-form-panel { min-height: 0; order: 1; align-items: flex-start; padding: 2.25rem 1.5rem 2rem; }
            .login-header { margin-bottom: 1.9rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>
<body>
    <canvas class="login-particles" aria-hidden="true"></canvas>
    <main class="login-page">
        <section class="login-visual" aria-labelledby="visual-title">
            <div class="login-brand">
                <picture>
                    <source srcset="{{ asset('images/logo/logo-256.webp') }}" type="image/webp">
                    <img class="login-brand-mark" src="{{ asset('images/logo/logo.png') }}" alt="">
                </picture>
                <div class="login-brand-copy">
                    <span class="login-brand-name">3DY App</span>
                    <span class="login-brand-group">Tridaya Group</span>
                </div>
            </div>

            <h2 id="visual-title">Technical operations, connected.</h2>

            <img
                class="login-illustration"
                src="{{ asset('images/Login-Page/AssetIlustrasi.png') }}"
                alt=""
                aria-hidden="true"
                loading="eager"
                decoding="async"
            >
        </section>

        <section class="login-form-panel" aria-labelledby="login-title">
            <div class="login-form-inner">
                <header class="login-header">
                    <h1 id="login-title">Welcome back</h1>
                    <p>Sign in to your 3DY account</p>
                </header>

                <x-auth-session-status
                    class="login-status"
                    role="status"
                    aria-live="polite"
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
                        <label for="email" class="login-label">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="name@company.com"
                            required
                            autofocus
                            autocomplete="username"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            aria-describedby="email-error"
                            class="login-input"
                        >
                        <div id="email-error" aria-live="polite">
                            <x-input-error :messages="$errors->get('email')" class="login-error" />
                        </div>
                    </div>

                    <div x-data="{ showPassword: false, capsLock: false }" class="login-field">
                        <label for="password" class="login-label">Password</label>
                        <div class="login-input-wrap">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="password-error caps-lock-warning"
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
                                <svg x-show="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.3-5.5 9.5-5.5 9.5 5.5 9.5 5.5-3.3 5.5-9.5 5.5S2.5 12 2.5 12Z" /><circle cx="12" cy="12" r="2.5" /></svg>
                                <svg x-show="showPassword" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" /><path d="M10.73 5.08A10.4 10.4 0 0 1 12 5c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M6.61 6.61A13.5 13.5 0 0 0 1 13s3 6 9 6a9.7 9.7 0 0 0 5.39-1.61" /><line x1="2" x2="22" y1="2" y2="22" /></svg>
                            </button>
                        </div>
                        <p id="caps-lock-warning" x-show="capsLock" x-cloak role="alert" class="login-caps-lock">Caps Lock is on</p>
                        <div id="password-error" aria-live="polite">
                            <x-input-error :messages="$errors->get('password')" class="login-error" />
                        </div>
                    </div>

                    <div class="login-options">
                        <label for="remember" class="login-remember">
                            <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="login-link">Forgot password?</a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        x-bind:disabled="isSubmitting"
                        x-bind:aria-busy="isSubmitting"
                        class="login-submit"
                    >
                        <span x-show="!isSubmitting">Sign in</span>
                        <span x-show="isSubmitting" x-cloak>Signing in...</span>
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
    </main>
    <script>
        (() => {
            const canvas = document.querySelector('.login-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const glowHex = (getComputedStyle(document.documentElement).getPropertyValue('--aurora-glow').trim() || '#0ea5e9').replace('#', '');
            const accent = [0, 2, 4].map((i) => parseInt(glowHex.substr(i, 2), 16)).join(',');

            let W = 0, H = 0, dots = [], raf = 0;

            const seed = () => {
                const count = Math.min(80, Math.floor((W * H) / 26000));
                dots = Array.from({ length: count }, () => ({
                    x: Math.random() * W,
                    y: Math.random() * H,
                    r: .6 + Math.random() * 1,
                    vx: (Math.random() - .5) * .22,
                    vy: (Math.random() - .5) * .22,
                    a: .12 + Math.random() * .28,
                }));
            };

            const size = () => {
                const dpr = Math.min(window.devicePixelRatio || 1, 2);
                W = window.innerWidth;
                H = window.innerHeight;
                canvas.width = Math.round(W * dpr);
                canvas.height = Math.round(H * dpr);
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                seed();
            };

            const frame = () => {
                ctx.clearRect(0, 0, W, H);
                for (let i = dots.length - 1; i >= 0; i--) {
                    const d = dots[i];
                    d.x = (d.x + d.vx + W) % W;
                    d.y = (d.y + d.vy + H) % H;
                    if (d.burst) {
                        d.a -= .003;
                        if (d.a <= 0) dots.splice(i, 1);
                    } else if (d.spawned) {
                        d.a -= .002;
                        if (d.a <= 0) dots.splice(i, 1);
                    }
                }
                ctx.lineWidth = 1;
                for (let i = 0; i < dots.length; i++) {
                    for (let j = i + 1; j < dots.length; j++) {
                        const dx = dots[i].x - dots[j].x;
                        const dy = dots[i].y - dots[j].y;
                        const dist = Math.hypot(dx, dy);
                        if (dist < 140) {
                            ctx.strokeStyle = `rgba(${accent},${((1 - dist / 140) * .22).toFixed(2)})`;
                            ctx.beginPath();
                            ctx.moveTo(dots[i].x, dots[i].y);
                            ctx.lineTo(dots[j].x, dots[j].y);
                            ctx.stroke();
                        }
                    }
                }
                for (const d of dots) {
                    ctx.beginPath();
                    ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(${accent},${d.a.toFixed(2)})`;
                    ctx.fill();
                }
                if (!reduce && !document.hidden) raf = requestAnimationFrame(frame);
            };

            size();
            frame();
            window.addEventListener('resize', () => { size(); if (reduce) frame(); });
            if (!reduce) {
                setInterval(() => {
                    if (document.hidden || dots.length >= 120) return;
                    for (let i = 0; i < 2; i++) {
                        dots.push({
                            x: Math.random() * W,
                            y: Math.random() * H,
                            r: .6 + Math.random() * 1,
                            vx: (Math.random() - .5) * .22,
                            vy: (Math.random() - .5) * .22,
                            a: .12 + Math.random() * .28,
                            spawned: true,
                        });
                    }
                }, 1200);
            }
            window.addEventListener('click', (e) => {
                if (reduce || e.target.closest('.login-page')) return;
                for (let i = 0; i < 8 && dots.length < 140; i++) {
                    const angle = Math.random() * Math.PI * 2;
                    const speed = 1 + Math.random() * 2.5;
                    dots.push({
                        x: e.clientX,
                        y: e.clientY,
                        r: .8 + Math.random() * 1.4,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed,
                        a: .3 + Math.random() * .4,
                        burst: true,
                    });
                }
                if (!raf) frame();
            });
        })();
    </script>
</body>
</html>
