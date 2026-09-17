<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - {{ config('app.name', 'Tridaya App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/kinetic-grid.js'])

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --login-navy: #1e40af;
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

        .login-page {
            display: grid;
            min-height: 100vh;
            min-height: 100dvh;
            grid-template-columns: minmax(22rem, 40%) 1fr;
            background: var(--login-paper);
        }

        .login-form-panel {
            display: flex;
            min-width: 0;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            padding: clamp(2rem, 6vw, 6rem) clamp(1.5rem, 6vw, 6.5rem);
        }

        .login-form-inner {
            width: min(100%, 23.75rem);
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
            margin-top: clamp(3rem, 8vh, 5.5rem);
        }

        .login-header h1 {
            margin: 0;
            color: var(--login-ink);
            font-size: clamp(1.5rem, 2.5vw, 1.75rem);
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

        .login-brand-panel {
            position: relative;
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            padding: 3rem;
        }

        .login-brand-panel::before {
            position: absolute;
            top: -12rem;
            right: -8rem;
            width: 32rem;
            height: 32rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            box-shadow: 0 0 0 2rem rgba(255, 255, 255, 0.025), 0 0 0 5rem rgba(255, 255, 255, 0.02);
            content: '';
            pointer-events: none;
        }

        .login-brand-panel::after {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.38) 1px, transparent 1.2px);
            background-size: 2rem 2rem;
            content: '';
            opacity: 0.1;
            pointer-events: none;
        }

        .login-brand-kinetic {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .login-brand-content {
            position: relative;
            z-index: 1;
            width: min(100%, 28rem);
            color: #ffffff;
            text-align: center;
        }

        .login-brand-content img {
            width: 4.5rem;
            height: 4.5rem;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.94;
        }

        .login-brand-content h2 {
            margin: 2rem 0 0;
            color: #ffffff;
            font-size: clamp(1.65rem, 3vw, 2.25rem);
            font-weight: 600;
            letter-spacing: -0.045em;
            line-height: 1.25;
        }

        .login-brand-content p {
            max-width: 23rem;
            margin: 1.1rem auto 0;
            color: #bfdbfe;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        @media (prefers-reduced-motion: no-preference) {
            .login-form-inner {
                animation: login-fade-in 500ms ease both;
            }
        }

        @keyframes login-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 1023px) {
            .login-page {
                display: block;
            }

            .login-form-panel {
                min-height: 100vh;
                min-height: 100dvh;
                padding: 2.5rem clamp(1.25rem, 8vw, 4rem);
            }
        }

        @media (max-width: 420px) {
            .login-form-panel {
                padding: 2rem 1.25rem;
            }

            .login-header {
                margin-top: 3.25rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
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
                    <h1 id="login-title">Selamat datang kembali</h1>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
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
                            <span class="login-label">Alamat Email</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="kamu@perusahaan.com"
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
                                <a href="{{ route('password.request') }}" class="login-link">Lupa password?</a>
                            @endif
                        </div>
                        <div class="login-input-wrap">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                placeholder="Masukkan password"
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
                                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
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
                        <p x-show="capsLock" x-cloak role="alert" class="mt-2 rounded-md bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">Caps Lock aktif</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <label for="remember" class="login-remember">
                        <input id="remember" type="checkbox" name="remember">
                        Ingat saya
                    </label>

                    <button
                        type="submit"
                        x-bind:disabled="isSubmitting"
                        x-bind:aria-busy="isSubmitting"
                        class="login-submit"
                    >
                        <span x-show="!isSubmitting">Masuk</span>
                        <span x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Memuat...
                        </span>
                        <svg x-show="!isSubmitting" class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="login-register">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="login-link">Daftar sekarang</a>
                    </p>
                @endif
            </div>
        </section>

        <aside class="login-brand-panel" aria-label="Tridaya App">
            <canvas id="login-kinetic" class="login-brand-kinetic" aria-hidden="true"></canvas>
            <div class="login-brand-content">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Tridaya App">
                <h2>Connecting People,<br>Empowering Business</h2>
                <p>Technology and communication that keeps your team connected.</p>
            </div>
        </aside>
    </main>
</body>
</html>
