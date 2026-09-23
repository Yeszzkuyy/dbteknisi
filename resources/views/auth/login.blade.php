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
            background: var(--login-surface);
            color: var(--login-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        .login-page {
            display: grid;
            grid-template-columns: minmax(0, 55%) minmax(22rem, 45%);
            min-height: 100dvh;
            overflow: hidden;
            background: var(--login-surface);
        }

        .login-visual {
            position: relative;
            display: flex;
            min-height: 100dvh;
            order: 1;
            flex-direction: column;
            overflow: hidden;
            padding: clamp(2rem, 5vw, 5.5rem) clamp(1.5rem, 6vw, 6rem) clamp(1.75rem, 4vw, 3.5rem);
            isolation: isolate;
            color: #f8fafc;
            background:
                linear-gradient(135deg, rgb(var(--accent-950) / 1), rgb(var(--accent-900) / .93)),
                var(--login-panel);
        }

        .login-visual::before {
            position: absolute;
            inset: 0;
            z-index: -2;
            background:
                linear-gradient(33deg, transparent 0 47%, rgb(var(--accent-300) / .055) 47.15% 47.3%, transparent 47.45% 100%),
                linear-gradient(147deg, transparent 0 53%, rgb(var(--accent-300) / .045) 53.15% 53.3%, transparent 53.45% 100%),
                repeating-linear-gradient(90deg, transparent 0 4.5rem, rgb(var(--accent-300) / .035) 4.55rem 4.6rem, transparent 4.65rem 9rem);
            content: "";
            opacity: .9;
        }

        .login-visual::after {
            position: absolute;
            right: -13rem;
            bottom: -14rem;
            z-index: -1;
            width: 34rem;
            height: 34rem;
            border: 1px solid rgb(var(--accent-300) / .14);
            border-radius: 50%;
            box-shadow: 0 0 0 3rem rgb(var(--accent-300) / .025), 0 0 0 7rem rgb(var(--accent-300) / .02);
            content: "";
        }

        .login-visual-content,
        .login-visual-footer { position: relative; z-index: 1; }

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

        .login-visual-copy { max-width: 30rem; margin-top: clamp(4rem, 11vh, 8rem); }

        .login-kicker {
            margin: 0;
            color: rgb(147 197 253 / .82);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .16em;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .login-visual-copy h2 {
            max-width: 24rem;
            margin: .85rem 0 0;
            color: #f8fafc;
            font-size: clamp(1.8rem, 3.5vw, 3.2rem);
            font-weight: 700;
            letter-spacing: -.055em;
            line-height: 1.06;
        }

        .login-visual-copy p:last-child {
            max-width: 25rem;
            margin: 1.1rem 0 0;
            color: rgb(226 232 240 / .7);
            font-size: .9rem;
            line-height: 1.7;
        }

        .login-topology-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(92%, 42rem);
            margin: auto auto 0;
            padding: 1rem 0 0;
        }

        .login-topology {
            display: block;
            width: 100%;
            height: auto;
            max-height: 34vh;
            color: rgb(147 197 253 / .78);
            object-fit: contain;
        }

        .topology-grid { color: rgb(191 219 254 / .12); }
        .topology-structure { color: rgb(191 219 254 / .5); }
        .topology-node { fill: rgb(147 197 253 / .9); stroke: var(--login-panel); stroke-width: 5; }
        .topology-node.is-primary { fill: rgb(var(--accent-400) / 1); }
        .topology-module { fill: rgb(var(--accent-950) / .4); stroke: rgb(191 219 254 / .48); }
        .topology-flow {
            stroke: rgb(147 197 253 / .85);
            stroke-dasharray: 3 13;
            animation: topology-flow 12s linear infinite;
        }

        .topology-node { transition: fill .5s ease; }
        .topology-labels { font-family: 'Plus Jakarta Sans', sans-serif; }

        .login-topology-wrap {
            transform: translate3d(var(--px, 0px), var(--py, 0px), 0);
            transition: transform .7s cubic-bezier(.16, 1, .3, 1);
        }

        .login-page.is-typing .topology-flow { animation-duration: 4s; }
        .login-page.is-typing .topology-node { fill: rgb(191 219 254 / 1); }
        .login-page.is-typing .topology-node.is-primary { fill: rgb(var(--accent-300) / 1); }

        .login-visual-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.25rem;
            padding-right: clamp(1.25rem, 4vw, 3rem);
            color: rgb(191 219 254 / .58);
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .login-visual-footer span:last-child { color: rgb(226 232 240 / .38); }

        .login-form-panel {
            position: relative;
            display: flex;
            min-width: 0;
            min-height: 100dvh;
            z-index: 2;
            order: 2;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 6vw, 6rem);
            isolation: isolate;
            background: var(--login-surface);
        }

        .login-form-panel::before {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: min(58%, 34rem);
            z-index: 0;
            background:
                linear-gradient(120deg, transparent 0 35%, rgb(var(--accent-600) / .08) 35.2% 35.35%, transparent 35.55% 100%),
                linear-gradient(28deg, transparent 0 58%, rgb(var(--accent-500) / .055) 58.2% 58.35%, transparent 58.55% 100%),
                repeating-linear-gradient(135deg, transparent 0 2.7rem, rgb(var(--accent-600) / .035) 2.75rem 2.8rem, transparent 2.85rem 5.6rem);
            content: "";
            opacity: .75;
            pointer-events: none;
            -webkit-mask-image: linear-gradient(to bottom, #000 0%, #000 26%, transparent 96%);
            mask-image: linear-gradient(to bottom, #000 0%, #000 26%, transparent 96%);
        }

        .login-form-panel::after {
            position: absolute;
            top: 0;
            left: -18rem;
            bottom: 0;
            width: 22rem;
            z-index: 0;
            background:
                linear-gradient(68deg, transparent 0 31%, rgb(var(--accent-600) / .05) 31.2% 31.4%, transparent 31.6% 100%),
                linear-gradient(16deg, transparent 0 58%, rgb(var(--accent-500) / .035) 58.2% 58.4%, transparent 58.6% 100%),
                radial-gradient(ellipse 75% 60% at 0% 50%, rgb(var(--accent-950) / .08) 0%, transparent 70%),
                linear-gradient(270deg, var(--login-surface) 0%, var(--login-surface) 32%, rgb(var(--accent-950) / .05) 58%, rgb(var(--accent-950) / .02) 79%, transparent 100%);
            content: "";
            pointer-events: none;
        }

        .login-form-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 25rem);
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
            font-size: .9rem;
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
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .login-input-wrap { position: relative; }

        .login-input {
            display: block;
            width: 100%;
            min-height: 3rem;
            padding: .72rem .85rem;
            border: 1px solid var(--login-border);
            border-radius: .55rem;
            outline: none;
            color: var(--login-text);
            background: var(--login-field);
            font: inherit;
            font-size: .87rem;
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
            font-size: .72rem;
            font-weight: 700;
        }

        .login-password-toggle:hover { color: var(--login-text); background: var(--nav-hover-bg); }

        .login-link {
            color: var(--login-accent);
            font-size: .76rem;
            font-weight: 700;
            text-decoration: none;
            text-underline-offset: 3px;
        }

        .login-link:hover { color: var(--login-accent-hover); text-decoration: underline; }

        .login-error {
            margin: 0;
            padding: 0;
            color: rgb(var(--semantic-danger) / 1);
            font-size: .75rem;
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
            font-size: .78rem;
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
            min-height: 3rem;
            align-items: center;
            justify-content: center;
            padding: .72rem 1rem;
            border: 1px solid var(--login-accent);
            border-radius: .55rem;
            color: #f8fafc;
            background: var(--login-accent);
            cursor: pointer;
            font: inherit;
            font-size: .83rem;
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
            font-size: .78rem;
            line-height: 1.5;
            text-align: center;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 3px solid var(--login-focus);
            outline-offset: 2px;
        }

        @keyframes topology-flow {
            to { stroke-dashoffset: -192; }
        }

        @media (prefers-reduced-motion: no-preference) {
            @keyframes login-rise {
                from { opacity: 0; transform: translateY(14px); }
                to { opacity: 1; transform: none; }
            }

            .login-brand { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) backwards; }
            .login-visual-copy { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) .08s backwards; }
            .login-form-inner { animation: login-rise .5s cubic-bezier(.16, 1, .3, 1) .1s backwards; }
            .login-topology-wrap { animation: login-rise .6s cubic-bezier(.16, 1, .3, 1) .16s backwards; }
        }

        @media (max-width: 1023px) {
            .login-page { grid-template-columns: minmax(18rem, 55%) minmax(18rem, 45%); }
            .login-visual { padding-inline: clamp(1.5rem, 4vw, 3rem); }
            .login-visual-copy { margin-top: clamp(3rem, 8vh, 5rem); }
            .login-visual-copy h2 { font-size: clamp(1.7rem, 3vw, 2.4rem); }
            .login-topology-wrap { margin-top: 2rem; }
            .login-form-panel { padding-inline: clamp(1.5rem, 5vw, 4rem); }
        }

        @media (max-width: 767px) {
            .login-page { display: flex; flex-direction: column; min-height: 100dvh; }
            .login-visual { min-height: 13.5rem; order: 2; padding: 1.5rem 1.25rem 1.2rem; }
            .login-visual::after { right: -14rem; bottom: -20rem; }
            .login-brand-mark { width: 3.5rem; height: 2.35rem; }
            .login-visual-copy { max-width: 19rem; margin-top: 2.1rem; }
            .login-visual-copy h2 { margin-top: .55rem; font-size: 1.65rem; }
            .login-visual-copy p:last-child { display: none; }
            .login-topology-wrap { position: absolute; right: -4rem; bottom: -1.3rem; width: 19rem; margin: 0; opacity: .72; }
            .login-visual-footer { margin-top: auto; font-size: .6rem; }
            .login-form-panel { min-height: 0; order: 1; align-items: flex-start; padding: 2.5rem 1.25rem 2rem; }
            .login-form-panel::after { display: none; }
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
    <main class="login-page">
        <section class="login-visual" aria-labelledby="visual-title">
            <div class="login-visual-content">
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

                <div class="login-visual-copy">
                    <p class="login-kicker">Operational intelligence platform</p>
                    <h2 id="visual-title">Technical operations, connected.</h2>
                    <p>One workspace for teams across sales, projects, finance, and field operations.</p>
                </div>
            </div>

            <div class="login-topology-wrap" aria-hidden="true">
                <svg class="login-topology" viewBox="0 0 680 460" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g class="topology-grid" stroke="currentColor" stroke-width="1">
                        <path d="M40 64H640M40 128H640M40 192H640M40 256H640M40 320H640M40 384H640" />
                        <path d="M104 24V424M168 24V424M232 24V424M296 24V424M360 24V424M424 24V424M488 24V424M552 24V424M616 24V424" />
                    </g>
                    <g class="topology-structure" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M74 332L194 332L270 252L360 252L448 158L590 158" />
                        <path d="M120 118L218 118L284 188L360 188L432 274L560 274" />
                        <path d="M194 332L194 382M270 252V336M360 188V252M448 158V84M560 274V354" />
                        <path d="M74 332V284H148M590 158V108H536" />
                    </g>
                    <path class="topology-flow" d="M74 332L194 332L270 252L360 252L448 158L590 158" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                    <g class="topology-module" stroke-width="2">
                        <rect x="44" y="262" width="92" height="44" rx="6" />
                        <rect x="152" y="356" width="84" height="42" rx="6" />
                        <rect x="230" y="210" width="80" height="44" rx="6" />
                        <rect x="320" y="156" width="80" height="44" rx="6" />
                        <rect x="416" y="62" width="82" height="44" rx="6" />
                        <rect x="524" y="326" width="92" height="44" rx="6" />
                        <rect x="540" y="136" width="92" height="44" rx="6" />
                    </g>
                    <g class="topology-node">
                        <circle cx="74" cy="332" r="7" />
                        <circle cx="194" cy="332" r="7" />
                        <circle cx="270" cy="252" r="7" />
                        <circle cx="360" cy="252" r="8" class="is-primary" />
                        <circle cx="448" cy="158" r="7" />
                        <circle cx="590" cy="158" r="7" />
                    </g>
                    <g fill="currentColor" opacity=".5">
                        <circle cx="148" cy="118" r="3" />
                        <circle cx="560" cy="274" r="3" />
                        <circle cx="360" cy="188" r="3" />
                        <circle cx="448" cy="84" r="3" />
                    </g>
                    <g class="topology-labels" fill="rgb(191 219 254 / .6)" font-size="13" font-weight="600" letter-spacing="2">
                        <text x="74" y="357" text-anchor="middle">LEAD</text>
                        <text x="194" y="315" text-anchor="middle">PROJECT</text>
                        <text x="282" y="272" text-anchor="start">INSTALASI</text>
                        <text x="348" y="240" text-anchor="end">INVOICE</text>
                        <text x="436" y="162" text-anchor="end">PAYMENT</text>
                        <text x="590" y="198" text-anchor="middle">LUNAS</text>
                    </g>
                </svg>
            </div>

            <footer class="login-visual-footer">
                <span>Tridaya Group</span>
                <span>3DY App</span>
            </footer>
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
            const page = document.querySelector('.login-page');
            const wrap = document.querySelector('.login-topology-wrap');
            if (!page) return;
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!reduce && wrap && window.matchMedia('(pointer: fine)').matches) {
                let raf = 0;
                page.addEventListener('mousemove', (e) => {
                    if (raf) return;
                    raf = requestAnimationFrame(() => {
                        raf = 0;
                        const r = page.getBoundingClientRect();
                        const x = (e.clientX - r.left) / r.width - 0.5;
                        const y = (e.clientY - r.top) / r.height - 0.5;
                        wrap.style.setProperty('--px', (x * 12).toFixed(2) + 'px');
                        wrap.style.setProperty('--py', (y * 10).toFixed(2) + 'px');
                    });
                });
                page.addEventListener('mouseleave', () => {
                    wrap.style.setProperty('--px', '0px');
                    wrap.style.setProperty('--py', '0px');
                });
            }
            page.querySelectorAll('.login-input').forEach((el) => {
                el.addEventListener('focus', () => page.classList.add('is-typing'));
                el.addEventListener('blur', () => page.classList.remove('is-typing'));
            });
        })();
    </script>
</body>
</html>
