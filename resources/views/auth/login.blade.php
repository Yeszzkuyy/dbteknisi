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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --login-blue:     #2563eb;
            --login-blue-mid: #3b52d9;
            --login-ink:      #111827;
            --login-muted:    #6b7280;
            --login-border:   #d1d5db;
            --panel-from:     #1a1464;
            --panel-mid:      #2d27b8;
            --panel-to:       #4338ca;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ================= LAYOUT ================= */
        .login-page { min-height: 100dvh; display: flex; }

        .login-shell {
            display: grid;
            width: 100%;
            min-height: 100dvh;
            grid-template-columns: 40% 60%;
            overflow: hidden;
            background: #ffffff;
        }

        /* ================= PANEL KIRI — FORM ================= */
        .login-form-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem clamp(1.5rem, 6vw, 3.5rem);
            background: #ffffff;
        }

        .login-form-inner {
            width: min(100%, 22rem);
            animation: login-fade-in 400ms ease-out;
        }

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

        .login-header { margin-bottom: 1.75rem; }

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
            background: linear-gradient(135deg, var(--login-blue), #4f46e5);
            border: none;
            border-radius: .55rem;
            cursor: pointer;
            letter-spacing: .015em;
            transition: box-shadow 180ms, transform 100ms, filter 180ms;
        }

        .login-submit:hover:not(:disabled) {
            filter: brightness(1.06);
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

        .login-status { margin-bottom: .75rem; }

        @keyframes login-fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ================= PANEL KANAN — ILUSTRASI MINIMALIS ================= */
        .login-brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: clamp(1rem, 3vh, 2rem);
            overflow: hidden;
            padding: clamp(1.5rem, 4vw, 3rem);
            background:
                radial-gradient(circle at 85% 12%, rgba(129,140,248,.35) 0%, transparent 42%),
                radial-gradient(circle at 8% 88%, rgba(59,82,217,.5) 0%, transparent 46%),
                linear-gradient(145deg, var(--panel-from) 0%, var(--panel-mid) 50%, var(--panel-to) 100%);
            border-radius: 2.5rem 0 0 2.5rem;
        }

        .login-brand-content {
            color: #ffffff;
            text-align: center;
            max-width: 26rem;
        }

        .login-brand-content h2 {
            font-size: clamp(1rem, 2vw, 1.35rem);
            font-weight: 700;
            line-height: 1.3;
        }

        .login-brand-content p {
            font-size: .82rem;
            opacity: .75;
            margin-top: .35rem;
        }

        /* Satu-satunya ilustrasi: inline SVG flat, tanpa request gambar */
        .login-illustration {
            width: min(78%, 24rem);
            height: auto;
            animation: float-y 6s ease-in-out infinite;
        }

        @keyframes float-y {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-.6rem); }
        }

        .login-badges {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            font-weight: 600;
            color: #e0e7ff;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            padding: .35rem .8rem;
            backdrop-filter: blur(4px);
        }

        .login-badge svg { width: .85rem; height: .85rem; }

        /* ================= RESPONSIVE ================= */
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
                min-height: clamp(14rem, 52vw, 19rem);
                border-radius: 0 0 2.5rem 2.5rem;
                gap: .8rem;
            }

            .login-brand-content p { display: none; }

            .login-illustration { width: min(64%, 15rem); }
            .login-badges { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-illustration,
            .login-form-inner { animation: none !important; }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <div class="login-shell">

            {{-- PANEL KIRI — FORM --}}
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

            {{-- PANEL KANAN — ILUSTRASI MINIMALIS (1 inline SVG, tanpa JS/canvas) --}}
            <aside class="login-brand-panel" aria-label="Tridaya App">
                <div class="login-brand-content">
                    <h2>Connecting People, Empowering Business</h2>
                    <p>Technology and communication that keeps your team connected.</p>
                </div>

                <svg class="login-illustration" viewBox="0 0 400 320" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Secure login illustration">
                    <!-- lingkaran latar -->
                    <circle cx="200" cy="160" r="130" fill="#ffffff" opacity="0.08"/>
                    <circle cx="200" cy="160" r="100" fill="#ffffff" opacity="0.08"/>
                    <!-- kartu browser -->
                    <rect x="90" y="80" width="220" height="160" rx="16" fill="#ffffff" opacity="0.97"/>
                    <rect x="90" y="80" width="220" height="34" rx="16" fill="#EEF2FF"/>
                    <rect x="90" y="98" width="220" height="16" fill="#EEF2FF"/>
                    <circle cx="108" cy="97" r="5" fill="#F87171"/>
                    <circle cx="122" cy="97" r="5" fill="#FBBF24"/>
                    <circle cx="136" cy="97" r="5" fill="#34D399"/>
                    <!-- baris form semu -->
                    <rect x="112" y="132" width="120" height="12" rx="6" fill="#E0E7FF"/>
                    <rect x="112" y="152" width="176" height="12" rx="6" fill="#E0E7FF"/>
                    <rect x="112" y="172" width="150" height="12" rx="6" fill="#E0E7FF"/>
                    <rect x="112" y="196" width="90" height="24" rx="12" fill="#2563EB"/>
                    <rect x="212" y="196" width="76" height="24" rx="12" fill="#E0E7FF"/>
                    <!-- lencana perisai -->
                    <circle cx="296" cy="216" r="40" fill="#FBBF24"/>
                    <circle cx="296" cy="216" r="40" fill="#000000" opacity="0.06"/>
                    <path d="M296 190l18 7v12c0 12-7.5 21-18 25-10.5-4-18-13-18-25v-12l18-7z" fill="#1E1B4B"/>
                    <path d="M289 215l5 5 9-11" stroke="#FBBF24" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- kartu user melayang -->
                    <rect x="52" y="196" width="96" height="52" rx="12" fill="#ffffff" opacity="0.95"/>
                    <circle cx="72" cy="216" r="10" fill="#C7D2FE"/>
                    <circle cx="72" cy="213" r="4" fill="#4F46E5"/>
                    <path d="M63 226c1.5-5 5-7 9-7s7.5 2 9 7" fill="#4F46E5"/>
                    <rect x="88" y="208" width="48" height="8" rx="4" fill="#E0E7FF"/>
                    <rect x="88" y="220" width="32" height="8" rx="4" fill="#EEF2FF"/>
                    <!-- gelembung chat -->
                    <rect x="276" y="66" width="84" height="44" rx="12" fill="#ffffff" opacity="0.95"/>
                    <path d="M292 110l-4 12 14-12h-10z" fill="#ffffff" opacity="0.95"/>
                    <circle cx="292" cy="88" r="4" fill="#4F46E5"/>
                    <circle cx="306" cy="88" r="4" fill="#C7D2FE"/>
                    <circle cx="320" cy="88" r="4" fill="#C7D2FE"/>
                    <!-- titik aksen -->
                    <circle cx="66" cy="96" r="6" fill="#F472B6" opacity="0.9"/>
                    <circle cx="340" cy="140" r="5" fill="#38BDF8" opacity="0.9"/>
                    <circle cx="330" cy="256" r="7" fill="#34D399" opacity="0.85"/>
                </svg>

                <div class="login-badges">
                    <span class="login-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.6 2A9 9 0 1112 3a9 9 0 018.6 9z"/></svg>
                        Secure Access
                    </span>
                    <span class="login-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Fast Workspace
                    </span>
                    <span class="login-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6-4a3 3 0 11-3-3"/></svg>
                        Team Connected
                    </span>
                </div>
            </aside>

        </div>
    </main>
</body>
</html>
