<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ config('app.name', 'Tridaya App') }}</title>

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

        /* Token port template Nexa (oklch -> hex). Satu-satunya sumber aksen auth. */
        :root {
            --nx-deep: #0a2272;
            --nx-blue: #0063e1;
            --nx-bright: #008bf6;
            --nx-glow: #30aff8;
            --nx-surface: #f5f7f9;
            --nx-card: #ffffff;
            --nx-input: #ffffff;
            --nx-line: #e5e8ed;
            --nx-ink: #171f2e;
            --nx-muted: rgba(23, 31, 46, 0.55);
            --nx-faint: rgba(23, 31, 46, 0.35);
            --nx-des: #e7000b;
            --nx-ring: rgba(0, 99, 225, 0.15);
        }

        .dark {
            --nx-surface: #020618;
            --nx-card: #0f172b;
            --nx-input: #333a4b;
            --nx-line: rgba(255, 255, 255, 0.1);
            --nx-ink: #f8fafc;
            --nx-muted: #90a1b9;
            --nx-faint: rgba(248, 250, 252, 0.35);
            --nx-des: #ff6467;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }

        .nx-auth {
            position: relative;
            display: flex;
            min-height: 100dvh;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 2.5rem 1rem;
            background: var(--nx-surface);
            color: var(--nx-ink);
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .nx-glow {
            position: absolute;
            z-index: 0;
            border-radius: 9999px;
            pointer-events: none;
            filter: blur(130px);
        }
        .nx-glow-a { top: -10rem; left: 25%; width: 480px; height: 480px; background: var(--nx-glow); opacity: 0.2; }
        .nx-glow-b { bottom: -12rem; right: 0; width: 440px; height: 440px; background: var(--nx-blue); opacity: 0.1; }

        .nx-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            overflow: hidden;
            border-radius: 24px;
            background: var(--nx-card);
            box-shadow:
                0 30px 80px -28px rgba(0, 60, 150, 0.3),
                0 2px 10px rgba(0, 30, 80, 0.05);
        }
        .dark .nx-card { box-shadow: 0 30px 80px -28px rgba(0, 0, 0, 0.7); }

        .nx-flex { display: flex; flex-direction: column; height: 100%; }
        @media (min-width: 1024px) {
            .nx-flex { flex-direction: row; }
            .nx-card { min-height: 640px; }
        }

        /* Panel brand */
        .nx-brand {
            position: relative;
            overflow: hidden;
            padding: 2.5rem 2rem;
            color: #f8fafc;
            background-image: linear-gradient(142deg, var(--nx-deep) 0%, var(--nx-blue) 58%, var(--nx-bright) 100%);
        }
        @media (min-width: 640px) { .nx-brand { padding: 2.5rem; } }
        @media (min-width: 1024px) {
            .nx-brand { display: flex; width: 42%; flex-direction: column; padding: 3rem 2.5rem; }
        }

        .nx-brand::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }
        .nx-brand-glow {
            position: absolute;
            top: -6rem;
            left: -4rem;
            width: 16rem;
            height: 16rem;
            border-radius: 9999px;
            background: var(--nx-glow);
            opacity: 0.25;
            filter: blur(90px);
            pointer-events: none;
        }
        .nx-wave {
            display: none;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            width: 120px;
            height: 100%;
            pointer-events: none;
        }
        @media (min-width: 1024px) { .nx-wave { display: block; } }
        @media (min-width: 1280px) { .nx-wave { width: 170px; } }

        .nx-brand-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }
        @media (min-width: 1024px) { .nx-brand-inner { justify-content: space-between; } }
        .nx-brand-top { margin-top: auto; margin-bottom: auto; }
        @media (min-width: 1024px) { .nx-brand-top { margin: auto 0; } }

        .nx-eyebrow { font-size: 0.875rem; font-weight: 500; color: rgba(255, 255, 255, 0.7); }
        .nx-logo-row { display: flex; flex-direction: column; align-items: flex-start; gap: 1.25rem; margin-top: 1.5rem; }
        .nx-logo { display: block; height: 2.75rem; width: auto; }
        .nx-product { font-size: 1.875rem; font-weight: 700; letter-spacing: -0.025em; color: #fff; line-height: 1.1; }
        .nx-product small { display: block; margin-top: 0.35rem; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.18em; color: rgba(255, 255, 255, 0.7); }
        .nx-tagline { margin: 1rem 0 0; max-width: 30ch; font-size: 0.875rem; line-height: 1.6; color: rgba(255, 255, 255, 0.7); }
        .nx-secure { display: none; margin-top: 2rem; font-family: ui-monospace, monospace; font-size: 10px; text-transform: uppercase; letter-spacing: 0.25em; color: rgba(255, 255, 255, 0.4); }
        @media (min-width: 1024px) { .nx-secure { display: block; } }

        .nx-tabs { display: flex; align-items: center; gap: 2rem; margin-top: 2rem; }
        @media (min-width: 1024px) { .nx-tabs { margin-top: 0; } }
        .nx-tab {
            position: relative;
            padding-bottom: 0.375rem;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .nx-tab:hover { color: rgba(255, 255, 255, 0.8); }
        .nx-tab::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2px;
            border-radius: 9999px;
            background: #fff;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .nx-tab-active { color: #fff; }
        .nx-tab-active::after { opacity: 1; }

        /* Panel form */
        .nx-form-wrap { display: flex; flex: 1; align-items: center; justify-content: center; padding: 2.5rem 1.5rem; }
        @media (min-width: 640px) { .nx-form-wrap { padding: 2.5rem 3rem; } }
        @media (min-width: 1024px) { .nx-form-wrap { padding: 2.5rem 3.5rem; } }
        .nx-form-inner { width: 100%; max-width: 380px; }

        .nx-h { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: -0.025em; color: var(--nx-ink); }
        @media (min-width: 640px) { .nx-h { font-size: 28px; } }
        .nx-sub { margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--nx-muted); }
        .nx-status {
            margin: 1.25rem 0 0;
            padding: 0.8rem 0.9rem;
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 0.55rem;
            background: rgba(34, 197, 94, 0.08);
            color: #15803d;
            font-size: 0.8rem;
            line-height: 1.5;
        }
        .dark .nx-status { color: #86efac; }

        .nx-form { display: flex; flex-direction: column; gap: 1.25rem; margin-top: 1.5rem; }
        .nx-field { display: flex; flex-direction: column; gap: 0.375rem; }
        .nx-label { font-size: 13px; font-weight: 500; color: var(--nx-muted); }
        .nx-input {
            display: block;
            width: 100%;
            height: 2.75rem;
            padding: 0 1rem;
            border: 1px solid var(--nx-line);
            border-radius: 0.75rem;
            outline: none;
            background: var(--nx-input);
            color: var(--nx-ink);
            font: inherit;
            font-size: 0.875rem;
            box-shadow: 0 1px 2px rgba(20, 30, 60, 0.04);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .nx-input::placeholder { color: var(--nx-faint); }
        .nx-input:hover { border-color: var(--nx-faint); }
        .nx-input:focus { border-color: var(--nx-blue); box-shadow: 0 0 0 4px var(--nx-ring); }
        .nx-input[aria-invalid="true"] { border-color: var(--nx-des); }
        .nx-input[aria-invalid="true"]:focus { box-shadow: 0 0 0 4px rgba(231, 0, 11, 0.15); }
        .nx-pass-wrap { position: relative; }
        .nx-pass-wrap .nx-input { padding-right: 2.75rem; }
        .nx-pass-toggle {
            position: absolute;
            top: 50%;
            right: 0.375rem;
            display: grid;
            place-items: center;
            width: 2rem;
            height: 2rem;
            transform: translateY(-50%);
            border: 0;
            border-radius: 0.5rem;
            background: transparent;
            color: var(--nx-faint);
            cursor: pointer;
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .nx-pass-toggle:hover { background: var(--nx-line); color: var(--nx-ink); }
        .nx-err { margin: 0; padding: 0; list-style: none; color: var(--nx-des); font-size: 0.75rem; font-weight: 500; line-height: 1.45; }
        .nx-caps { margin: 0; color: #b45309; font-size: 0.75rem; }
        .dark .nx-caps { color: #fdba74; }

        .nx-row { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; }
        .nx-check { display: inline-flex; align-items: center; gap: 0.55rem; color: var(--nx-muted); font-size: 0.82rem; cursor: pointer; user-select: none; }
        .nx-check input { width: 1rem; height: 1rem; margin: 0; accent-color: var(--nx-blue); cursor: pointer; }
        .nx-link { color: var(--nx-blue); font-size: 0.82rem; font-weight: 700; text-decoration: none; }
        .nx-link:hover { text-decoration: underline; }

        .nx-btn {
            display: inline-flex;
            width: 100%;
            min-height: 2.75rem;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            border-radius: 9999px;
            font: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
        }
        .nx-btn-primary {
            border: 1px solid var(--nx-blue);
            background-image: linear-gradient(135deg, var(--nx-blue) 0%, var(--nx-bright) 100%);
            color: #fff;
            box-shadow: 0 8px 20px -8px rgba(0, 99, 225, 0.7);
        }
        .nx-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 26px -8px rgba(0, 99, 225, 0.75); }
        .nx-btn-primary:active:not(:disabled) { transform: translateY(0); }
        .nx-btn-primary:disabled { cursor: not-allowed; opacity: 0.6; pointer-events: none; }
        .nx-btn-secondary { border: 1px solid var(--nx-line); background: var(--nx-card); color: var(--nx-muted); }
        .nx-btn-secondary:hover { transform: translateY(-2px); border-color: var(--nx-faint); color: var(--nx-ink); }
        .nx-btn-stack { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.25rem; }

        a.nx-btn:focus-visible, button.nx-btn:focus-visible, .nx-input:focus-visible, .nx-tab:focus-visible {
            outline: 3px solid var(--nx-ring);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: no-preference) {
            @keyframes nx-rise {
                from { opacity: 0; transform: translateY(16px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes nx-wave-drift {
                0%, 100% { transform: translateX(0); }
                50% { transform: translateX(5px); }
            }
            @keyframes nx-glow-breathe {
                0%, 100% { opacity: 0.55; }
                50% { opacity: 0.9; }
            }
            .nx-rise { animation: nx-rise 0.65s cubic-bezier(0.32, 0.72, 0, 1) both; }
            .nx-rise-late { animation: nx-rise 0.65s cubic-bezier(0.32, 0.72, 0, 1) 0.16s both; }
            .nx-wave { animation: nx-wave-drift 10s ease-in-out infinite; }
            .nx-brand-glow { animation: nx-glow-breathe 9s ease-in-out infinite; }
        }

        @media (prefers-reduced-motion: reduce) {
            .nx-auth *, .nx-auth *::before, .nx-auth *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body class="nx-auth">
    <div class="nx-glow nx-glow-a" aria-hidden="true"></div>
    <div class="nx-glow nx-glow-b" aria-hidden="true"></div>

    <main class="nx-card nx-rise">
        <div class="nx-flex">
            <section class="nx-brand" aria-label="Product">
                <div class="nx-brand-glow" aria-hidden="true"></div>
                <svg class="nx-wave" viewBox="0 0 170 640" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M64 0c46 74-32 148 22 224s-26 150 28 226c34 48 16 96 4 190H170V0Z" fill="rgba(255,255,255,0.14)" />
                    <path d="M96 0c44 70-28 146 24 222s-24 152 28 228c32 48 14 96 2 190H170V0Z" fill="rgba(255,255,255,0.34)" />
                    <path d="M128 0c38 66-24 144 26 220s-22 154 26 230c28 46 10 96 0 190H170V0Z" fill="var(--nx-card)" />
                </svg>

                <div class="nx-brand-inner">
                    <div class="nx-brand-top">
                        <p class="nx-eyebrow">Welcome to</p>
                        <div class="nx-logo-row">
                            <img class="nx-logo" src="{{ asset('images/logo/logo.png') }}" alt="3DY App logo">
                            <p class="nx-product">3DY App<small>Tridaya Group</small></p>
                        </div>
                        <p class="nx-tagline">Manage your workspace, data, and applications from one place.</p>
                        <p class="nx-secure">Secure &middot; Encrypted channel</p>
                    </div>

                    @php($isRegister = request()->routeIs('register'))
                    <nav class="nx-tabs" aria-label="Authentication mode">
                        <a href="{{ route('register') }}"
                           @if($isRegister) aria-current="page" @endif
                           class="nx-tab {{ $isRegister ? 'nx-tab-active' : '' }}">Create account</a>
                        <a href="{{ route('login') }}"
                           @unless($isRegister) aria-current="page" @endunless
                           class="nx-tab {{ $isRegister ? '' : 'nx-tab-active' }}">Login</a>
                    </nav>
                </div>
            </section>

            <div class="nx-form-wrap">
                <div class="nx-form-inner nx-rise-late">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>
</body>
</html>
