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
            --login-panel: rgb(var(--accent-950) / 1);
            --login-accent: rgb(var(--accent-600) / 1);
            --login-accent-hover: rgb(var(--accent-700) / 1);
            --login-surface: var(--bg);
            --login-field: var(--card-bg);
            --login-border: var(--input-border);
            --login-text: var(--text-primary);
            --login-muted: var(--text-secondary);
            --login-placeholder: var(--text-muted);
            --login-focus: rgb(var(--accent-500) / .28);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body { min-height: 100%; margin: 0; }

        body {
            display: grid;
            min-height: 100dvh;
            padding: 2rem 1.25rem;
            place-items: center;
            background: var(--login-surface);
            color: var(--login-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(ellipse 60% 45% at 50% 42%, rgb(var(--accent-500) / .14), transparent 70%);
            content: "";
            pointer-events: none;
        }

        .dark body::before { opacity: .5; }

        .login-particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .login-page {
            position: relative;
            z-index: 1;
            display: grid;
            width: min(72rem, 100%);
            overflow: hidden;
            grid-template-columns: minmax(0, 45%) minmax(0, 55%);
            border-radius: 1.75rem;
            background: var(--login-surface);
            box-shadow: 0 24px 80px -24px rgb(var(--accent-950) / .35), 0 4px 16px rgb(var(--accent-950) / .12);
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
            isolation: isolate;
            color: #f8fafc;
            background:
                linear-gradient(135deg, rgb(var(--accent-950) / 1), rgb(var(--accent-900) / .93)),
                var(--login-panel);
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
            font-size: clamp(1.75rem, 3vw, 2.35rem);
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
            border: 1px solid var(--login-border);
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

        .login-input:focus {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 3px var(--login-focus);
        }

        .login-input[aria-invalid="true"] {
            border-color: rgb(var(--semantic-danger) / .8);
        }

        .login-input-password { padding-right: 4.4rem; }

        .login-password-toggle {
            position: absolute;
            top: 50%;
            right: .55rem;
            min-width: 3.3rem;
            padding: .35rem .45rem;
            transform: translateY(-50%);
            border: 0;
            border-radius: .4rem;
            color: var(--login-muted);
            background: transparent;
            cursor: pointer;
            font: inherit;
            font-size: .8rem;
            font-weight: 700;
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

            .login-page { grid-template-columns: 1fr; border-radius: 1.25rem; }
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
                                <span x-text="showPassword ? 'Hide' : 'Show'"></span>
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
            const accent = (getComputedStyle(document.documentElement).getPropertyValue('--accent-500').trim() || '59 130 246').split(/\s+/).join(',');

            let W = 0, H = 0, dots = [], raf = 0;

            const seed = () => {
                const count = Math.min(90, Math.floor((W * H) / 22000));
                dots = Array.from({ length: count }, () => ({
                    x: Math.random() * W,
                    y: Math.random() * H,
                    r: .8 + Math.random() * 1.4,
                    vx: (Math.random() - .5) * .22,
                    vy: (Math.random() - .5) * .22,
                    a: .15 + Math.random() * .35,
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
                for (const d of dots) {
                    d.x = (d.x + d.vx + W) % W;
                    d.y = (d.y + d.vy + H) % H;
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
        })();
    </script>
</body>
</html>
