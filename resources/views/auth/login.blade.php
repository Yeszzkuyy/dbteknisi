<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --login-paper: #f7f9fc;
            --login-ink: #172554;
            --login-muted: #64748b;
            --login-blue: #2563eb;
            --login-indigo: #4f46e5;
            --login-line: #dbe3ef;
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

        .login-page {
            position: relative;
            display: flex;
            min-height: 100vh;
            min-height: 100dvh;
            overflow: hidden;
            background: var(--login-paper);
        }

        .login-visual {
            position: absolute;
            z-index: 0;
            inset: 0;
            overflow: hidden;
            background: #2f2ac4;
        }

        .login-visual-image {
            position: absolute;
            top: -4%;
            right: -5%;
            bottom: -4%;
            left: 24%;
            width: auto;
            height: 108%;
            max-width: none;
            object-fit: cover;
            object-position: center;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0) scale(1.04);
            transition: transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .login-visual-fade {
            position: absolute;
            z-index: 2;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, var(--login-paper) 0%, rgba(247, 249, 252, 0.98) 13%, rgba(247, 249, 252, 0.78) 24%, rgba(247, 249, 252, 0) 48%),
                linear-gradient(0deg, rgba(15, 23, 42, 0.16), transparent 28%, rgba(15, 23, 42, 0.08));
        }

        .visual-layer {
            position: absolute;
            z-index: 3;
            pointer-events: none;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
            transition: transform 900ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .visual-glow {
            top: 6%;
            right: -5%;
            width: min(42vw, 38rem);
            height: min(42vw, 38rem);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.25), rgba(115, 107, 255, 0.05) 42%, transparent 70%);
            filter: blur(8px);
            opacity: 0.72;
            animation: visual-breathe 8s ease-in-out infinite alternate;
        }

        .visual-orbit {
            top: 13%;
            right: 8%;
            width: min(38vw, 34rem);
            height: min(38vw, 34rem);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgba(255, 255, 255, 0.035), 0 0 0 56px rgba(255, 255, 255, 0.025);
        }

        .visual-orbit::before,
        .visual-orbit::after {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            content: '';
        }

        .visual-orbit::before {
            top: 18%;
            right: -8%;
            width: 2.75rem;
            height: 2.75rem;
            background: rgba(255, 255, 255, 0.08);
        }

        .visual-orbit::after {
            bottom: 10%;
            left: -5%;
            width: 1rem;
            height: 1rem;
            background: #f9a8d4;
            box-shadow: 0 0 22px rgba(249, 168, 212, 0.9);
        }

        .visual-network {
            top: 11%;
            right: 17%;
            width: min(31vw, 28rem);
            height: min(31vw, 28rem);
            opacity: 0.24;
        }

        .visual-network svg {
            width: 100%;
            height: 100%;
        }

        .visual-copy {
            position: absolute;
            z-index: 4;
            right: clamp(1.5rem, 7vw, 7rem);
            bottom: clamp(2rem, 8vh, 6rem);
            width: min(22rem, 30vw);
            color: #ffffff;
            text-align: right;
        }

        .visual-copy-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.76);
            font-size: 0.67rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .visual-copy-label::before {
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 50%;
            background: #f9a8d4;
            box-shadow: 0 0 14px rgba(249, 168, 212, 0.9);
            content: '';
        }

        .visual-copy h2 {
            margin: 0.7rem 0 0;
            color: #ffffff;
            font-size: clamp(1.15rem, 2.3vw, 2rem);
            font-weight: 700;
            letter-spacing: -0.04em;
            line-height: 1.18;
        }

        .visual-copy p {
            margin: 0.65rem 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-size: 0.78rem;
            line-height: 1.6;
        }

        .login-panel {
            position: relative;
            z-index: 5;
            display: flex;
            width: min(53vw, 680px);
            min-height: 100vh;
            min-height: 100dvh;
            flex-direction: column;
            justify-content: center;
            padding: clamp(2.25rem, 7vw, 7rem) clamp(2rem, 5vw, 5.5rem) clamp(2rem, 5vw, 4.5rem) clamp(1.5rem, 7vw, 7rem);
            background: linear-gradient(90deg, rgba(247, 249, 252, 0.99) 0%, rgba(247, 249, 252, 0.97) 61%, rgba(247, 249, 252, 0.75) 84%, rgba(247, 249, 252, 0) 100%);
        }

        .login-texture {
            position: absolute;
            z-index: -1;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            opacity: 0.34;
            pointer-events: none;
            mask-image: linear-gradient(90deg, #000 0%, #000 65%, transparent 100%);
        }

        .login-panel-inner {
            width: min(100%, 23rem);
        }

        .login-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            color: var(--login-ink);
        }

        .login-brand-mark {
            display: inline-flex;
            width: 2.65rem;
            height: 2.65rem;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #dbe4ff;
            border-radius: 0.85rem;
            background: rgba(255, 255, 255, 0.72);
        }

        .login-brand-mark img {
            width: 1.9rem;
            height: 1.9rem;
            object-fit: contain;
        }

        .login-brand-fallback {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #2563eb);
        }

        .login-heading {
            margin-top: clamp(2.75rem, 6vh, 4.75rem);
            animation: login-rise 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .login-eyebrow {
            color: var(--login-indigo);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .login-title {
            max-width: 22rem;
            margin: 0.7rem 0 0;
            color: var(--login-ink);
            font-size: clamp(1.9rem, 3vw, 2.55rem);
            font-weight: 800;
            letter-spacing: -0.055em;
            line-height: 1.08;
        }

        .login-subtitle {
            max-width: 22rem;
            margin: 0.9rem 0 0;
            color: var(--login-muted);
            font-size: 0.87rem;
            line-height: 1.7;
        }

        .login-form {
            width: 100%;
            margin-top: 2rem;
            animation: login-rise 0.55s 0.08s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .login-field-group + .login-field-group {
            margin-top: 1.15rem;
        }

        .login-field-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.5rem;
            color: #334155;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .login-field {
            display: flex;
            min-height: 3.35rem;
            align-items: center;
            gap: 0.7rem;
            border: 1px solid var(--login-line);
            border-radius: 0.85rem;
            background: rgba(255, 255, 255, 0.68);
            padding: 0 0.85rem;
            transition: border-color 180ms ease, background-color 180ms ease, box-shadow 180ms ease;
        }

        .login-field:focus-within {
            border-color: #818cf8;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.13);
        }

        .login-field-icon {
            width: 1.1rem;
            height: 1.1rem;
            flex: none;
            color: #818cf8;
        }

        .login-field input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--login-ink);
            font-size: 0.83rem;
        }

        .login-field input::placeholder {
            color: #7c8ba1;
        }

        .login-field input:focus {
            box-shadow: none;
        }

        .login-link {
            color: #4338ca;
            font-size: 0.73rem;
            font-weight: 700;
            transition: color 160ms ease;
        }

        .login-link:hover {
            color: #1e1b4b;
        }

        .login-submit {
            min-height: 3.35rem;
            border-radius: 0.85rem;
            box-shadow: 0 12px 22px -13px rgba(37, 99, 235, 0.82);
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        @media (hover: hover) {
            .login-submit:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 17px 28px -14px rgba(37, 99, 235, 0.9);
            }
        }

        .login-submit:disabled {
            cursor: wait;
        }

        .login-register {
            margin: 1.35rem 0 0;
            color: var(--login-muted);
            font-size: 0.76rem;
            line-height: 1.5;
            text-align: center;
        }

        .login-footer {
            display: flex;
            max-width: 23rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: clamp(2.25rem, 7vh, 5rem);
            color: #8a98aa;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .login-footer span:last-child {
            color: #a1acba;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: none;
        }

        @keyframes login-rise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes visual-breathe {
            from { opacity: 0.5; transform: scale(0.98); }
            to { opacity: 0.8; transform: scale(1.03); }
        }

        @media (max-width: 900px) {
            .login-page {
                display: flex;
                min-height: 100vh;
                min-height: 100dvh;
                flex-direction: column;
                overflow-x: hidden;
                overflow-y: auto;
            }

            .login-visual {
                position: relative;
                order: 1;
                width: 100%;
                height: clamp(15rem, 52vw, 22rem);
                min-height: 15rem;
                flex: none;
            }

            .login-visual-image {
                top: -5%;
                right: -4%;
                bottom: -5%;
                left: -4%;
                width: 108%;
                height: 110%;
                object-position: center 42%;
                transform: scale(1.03);
            }

            .login-visual-fade {
                background:
                    linear-gradient(180deg, rgba(247, 249, 252, 0) 45%, rgba(247, 249, 252, 0.72) 82%, var(--login-paper) 100%),
                    linear-gradient(0deg, rgba(15, 23, 42, 0.14), transparent 45%);
            }

            .visual-glow {
                top: -16%;
                right: -18%;
                width: 20rem;
                height: 20rem;
            }

            .visual-orbit {
                top: 8%;
                right: 10%;
                width: 16rem;
                height: 16rem;
            }

            .visual-network {
                top: 7%;
                right: 14%;
                width: 14rem;
                height: 14rem;
            }

            .visual-copy {
                right: 1.25rem;
                bottom: 1.25rem;
                width: min(16rem, 62vw);
            }

            .visual-copy h2 {
                font-size: 1.05rem;
            }

            .visual-copy p {
                font-size: 0.7rem;
            }

            .login-panel {
                order: 2;
                width: 100%;
                min-height: 0;
                justify-content: flex-start;
                padding: 2.15rem clamp(1.25rem, 7vw, 3.5rem) 2.35rem;
                background: linear-gradient(180deg, var(--login-paper) 0%, rgba(247, 249, 252, 0.98) 100%);
            }

            .login-texture {
                opacity: 0.26;
                mask-image: linear-gradient(180deg, #000 0%, transparent 88%);
            }

            .login-heading {
                margin-top: 2.2rem;
            }

            .login-footer {
                margin-top: 2.35rem;
            }
        }

        @media (max-width: 420px) {
            .login-visual {
                height: 14.5rem;
                min-height: 14.5rem;
            }

            .visual-copy {
                left: 1.25rem;
                width: auto;
                text-align: left;
            }

            .login-title {
                font-size: 1.85rem;
            }

            .login-footer {
                align-items: flex-start;
                flex-direction: column;
                gap: 0.4rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-heading,
            .login-form,
            .login-visual-image,
            .visual-glow {
                animation: none;
                transition: none;
            }

            .visual-layer {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <main
        class="login-page"
        x-data="{
            reducedMotion: false,
            coarsePointer: false,
            raf: null,
            init() {
                this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                this.coarsePointer = window.matchMedia('(pointer: coarse)').matches;
            },
            move(event) {
                if (this.reducedMotion || this.coarsePointer) return;

                const x = (event.clientX / window.innerWidth - 0.5) * 2;
                const y = (event.clientY / window.innerHeight - 0.5) * 2;
                cancelAnimationFrame(this.raf);
                this.raf = requestAnimationFrame(() => {
                    const layers = [
                        [this.$refs.image, -7, -5],
                        [this.$refs.glow, 14, 10],
                        [this.$refs.orbit, -18, -12],
                        [this.$refs.network, 10, 7],
                    ];

                    layers.forEach(([element, xFactor, yFactor]) => {
                        if (!element) return;
                        element.style.setProperty('--parallax-x', (x * xFactor) + 'px');
                        element.style.setProperty('--parallax-y', (y * yFactor) + 'px');
                    });
                });
            },
            reset() {
                if (this.reducedMotion || this.coarsePointer) return;
                cancelAnimationFrame(this.raf);
                [this.$refs.image, this.$refs.glow, this.$refs.orbit, this.$refs.network].forEach((element) => {
                    if (!element) return;
                    element.style.setProperty('--parallax-x', '0px');
                    element.style.setProperty('--parallax-y', '0px');
                });
            }
        }"
    >
        <div
            class="login-visual"
            aria-hidden="true"
            x-on:pointermove="move($event)"
            x-on:pointerleave="reset()"
        >
            <img
                x-ref="image"
                src="{{ asset('images/logo/Desain Ilustrasi.jpg') }}"
                alt=""
                class="login-visual-image"
                fetchpriority="high"
                decoding="async"
            >
            <div class="login-visual-fade"></div>
            <div x-ref="glow" class="visual-layer visual-glow"></div>
            <div x-ref="orbit" class="visual-layer visual-orbit"></div>
            <div x-ref="network" class="visual-layer visual-network">
                <svg viewBox="0 0 320 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M35 203L115 70L238 104L282 237L150 270L35 203Z" stroke="white" stroke-width="1" stroke-dasharray="5 8" />
                    <path d="M115 70L150 270M238 104L35 203M282 237L115 70" stroke="white" stroke-width="1" opacity="0.55" />
                    <circle cx="35" cy="203" r="4" fill="#F9A8D4" />
                    <circle cx="115" cy="70" r="4" fill="#C4B5FD" />
                    <circle cx="238" cy="104" r="4" fill="#FDE68A" />
                    <circle cx="282" cy="237" r="4" fill="#BAE6FD" />
                    <circle cx="150" cy="270" r="4" fill="#FCA5A5" />
                </svg>
            </div>
            <div class="visual-copy">
                <span class="visual-copy-label">PTNTI / Tridaya</span>
                <h2>Connecting People, Empowering Business</h2>
                <p>Technology and communication that keeps your team connected.</p>
            </div>
        </div>

        <section class="login-panel" aria-labelledby="login-title">
            <svg class="login-texture" viewBox="0 0 520 920" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <pattern id="login-technical-pattern" width="120" height="120" patternUnits="userSpaceOnUse">
                        <path d="M-18 37C12 4 43 4 72 37s55 33 84 0" fill="none" stroke="#6470C0" stroke-width="0.8" opacity="0.24" />
                        <path d="M18 0v20l18 18v28l20 20v34" fill="none" stroke="#6470C0" stroke-width="0.7" opacity="0.18" />
                        <path d="M104 0v28l-18 18v26l-20 20v28" fill="none" stroke="#6470C0" stroke-width="0.7" opacity="0.16" />
                        <circle cx="18" cy="20" r="1.5" fill="#6470C0" opacity="0.32" />
                        <circle cx="56" cy="66" r="1.5" fill="#6470C0" opacity="0.24" />
                        <circle cx="86" cy="72" r="1.5" fill="#6470C0" opacity="0.26" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#login-technical-pattern)" />
            </svg>

            <div class="login-panel-inner">
                <div class="login-brand">
                    <div x-data="{ logoFailed: false }" class="login-brand-mark">
                        <img
                            x-show="!logoFailed"
                            x-on:error="logoFailed = true"
                            src="{{ asset('images/logo/logo-lightmode.png') }}"
                            alt="PTNTI Tridaya"
                        >
                        <span x-show="logoFailed" x-cloak class="login-brand-mark login-brand-fallback">T</span>
                    </div>
                    <span class="text-sm font-extrabold tracking-tight">Tridaya App</span>
                </div>

                <div class="login-heading">
                    <p class="login-eyebrow">Welcome back</p>
                    <h1 id="login-title" class="login-title">Sign in to your account</h1>
                    <p class="login-subtitle">Access your workspace and keep your work connected.</p>
                </div>

                <x-auth-session-status
                    class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-left text-sm text-emerald-700"
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

                    <div class="login-field-group">
                        <label for="email" class="login-field-label">Email address</label>
                        <div class="login-field">
                            <svg class="login-field-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@company.com"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div x-data="{ showPassword: false, capsLock: false }" class="login-field-group">
                        <div class="login-field-label">
                            <label for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-link">Forgot password?</a>
                            @endif
                        </div>
                        <div class="login-field">
                            <svg class="login-field-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                x-on:keyup="capsLock = $event.getModifierState('CapsLock')"
                                x-on:keydown="capsLock = $event.getModifierState('CapsLock')"
                            >
                            <button
                                type="button"
                                x-on:click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showPassword"
                                aria-controls="password"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p x-show="capsLock" x-cloak role="alert" class="mt-2 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800">
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.5m0 3h.01M10.3 3.8L2.9 17a2 2 0 001.75 3h14.7a2 2 0 001.75-3L13.7 3.8a2 2 0 00-3.5 0z" />
                            </svg>
                            <span>Caps Lock is on</span>
                        </p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-5 flex items-center justify-between gap-4">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Remember me
                        </label>
                    </div>

                    <button
                        type="submit"
                        x-bind:disabled="isSubmitting"
                        x-bind:aria-busy="isSubmitting"
                        class="login-submit mt-6 flex w-full items-center justify-center gap-2 bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-200 disabled:opacity-75"
                    >
                        <span x-show="!isSubmitting">Sign in</span>
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

            <div class="login-footer">
                <span>PTNTI / Tridaya</span>
                <span>Internal workspace</span>
            </div>
        </section>
    </main>
</body>
</html>
