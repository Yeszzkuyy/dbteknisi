<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Web App Engineer</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ==============================================================
           CARA GANTI VIDEO BACKGROUND:
           - Taruh file video di: public/videos/backgrounds/
           - Edit array "videos" di x-data bawah (boleh kurang/lebih dari 5)
           - Ganti angka 8000 di setInterval() untuk durasi (ms)

           CARA GANTI WARNA TEMA (universal, cukup 1 tempat):
           - Ubah nilai --accent di :root di bawah ini.
           - Semua tombol/border/icon otomatis ikut warna itu.
           - Overlay background dibuat NETRAL (hitam transparan),
             jadi cocok dipasangkan video tema apapun tanpa nge-clash.
           ============================================================== */

        :root {
            /* GANTI WARNA AKSEN DI SINI SAJA */
            --accent: 37, 99, 235;        /* format: R, G, B (blue-600 = #2563EB) */
            --accent-strong: 29, 78, 216; /* blue-700 = #1D4ED8 */

            /* contoh warna lain yang bisa dipakai tinggal copy-paste ke atas:
               ungu     : --accent: 168, 130, 255;  --accent-strong: 139, 92, 246;
               hijau    : --accent: 74, 222, 128;   --accent-strong: 34, 197, 94;
               oranye   : --accent: 251, 146, 60;   --accent-strong: 249, 115, 22;
               merah muda: --accent: 244, 114, 182; --accent-strong: 236, 72, 153;
               netral abu: --accent: 203, 213, 225; --accent-strong: 148, 163, 184;
            */
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Figtree', sans-serif;
        }

        .bg-video-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            z-index: 1;
        }

        .bg-video-layer.is-active {
            opacity: 1;
        }

        /* fallback netral, bukan ungu lagi, biar cocok video apapun */
        .bg-fallback-gradient {
            position: absolute;
            inset: 0;
            z-index: 0;
            background: linear-gradient(160deg, #1a1a1a 0%, #0a0a0a 60%, #000000 100%);
        }

        /* overlay netral: gradasi hitam transparan, tidak nge-tint warna apapun */
        .bg-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(to bottom, rgba(0,0,0,0.45), rgba(0,0,0,0.7));
        }

        .glass-card {
            background: rgba(15, 15, 15, 0.55);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(var(--accent), 0.18);
        }

        .input-glass {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.14);
            transition: border-color .2s ease;
        }

        .input-glass:focus-within {
            border-color: rgba(var(--accent), 0.7);
        }

        .accent-text {
            color: rgb(var(--accent));
        }

        .accent-icon {
            color: rgba(var(--accent), 0.85);
        }

        .accent-btn {
            background: linear-gradient(to right, rgb(var(--accent-strong)), rgb(var(--accent)));
        }

        .accent-btn:hover {
            filter: brightness(1.1);
        }

        .accent-checkbox {
            accent-color: rgb(var(--accent));
        }
    </style>
</head>
<body class="text-white antialiased">

    <div
        x-data="{
            videos: {{ Illuminate\Support\Js::from([
                'videos/backgrounds/scene-1.mp4',
                'videos/backgrounds/scene-2.mp4',
                'videos/backgrounds/scene-3.mp4',
                'videos/backgrounds/scene-4.mp4',
                'videos/backgrounds/scene-5.mp4',
            ]) }},
            activeIndex: 0,
            reducedMotion: false,
            timer: null,
            init() {
                this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (this.reducedMotion || this.videos.length <= 1) return;
                this.timer = setInterval(() => {
                    this.activeIndex = (this.activeIndex + 1) % this.videos.length;
                }, 8000);
            }
        }"
        class="relative min-h-screen w-full overflow-hidden flex items-center justify-center px-4 py-10"
    >
        <!-- fallback dasar netral, selalu tampil paling belakang -->
        <div class="bg-fallback-gradient"></div>

        <!-- layer video, hanya jalan kalau bukan reduced-motion -->
        <template x-if="!reducedMotion">
            <template x-for="(src, index) in videos" :key="index">
                <video
                    :src="'{{ asset('') }}' + src"
                    class="bg-video-layer"
                    :class="{ 'is-active': activeIndex === index }"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                    x-on:error="$el.style.display = 'none'"
                ></video>
            </template>
        </template>

        <!-- overlay netral biar teks kebaca, tidak nge-tint warna -->
        <div class="bg-overlay"></div>

        <!-- ============ CARD LOGIN ============ -->
        <div class="relative z-10 w-full max-w-md">

            <div class="glass-card rounded-2xl shadow-2xl p-5 sm:p-8">

                <!-- Logo + Branding -->
                <div class="flex flex-col items-center text-center mb-6">
                    <div
                        x-data="{ logoFailed: false }"
                        class="mb-3"
                    >
                        {{-- LOGO: ganti file di public/images/logo.png --}}
                        <img
                            x-show="!logoFailed"
                            x-on:error="logoFailed = true"
                            src="{{ asset('images/logo.png') }}"
                            alt="Logo"
                            class="w-14 h-14 object-contain"
                        >
                        <div
                            x-show="logoFailed"
                            x-cloak
                            class="accent-btn w-14 h-14 rounded-xl flex items-center justify-center text-xl font-bold"
                        >W</div>
                    </div>
                    <h1 class="text-2xl font-bold">Web App Engineer</h1>
                    <p class="text-sm text-white/60 mt-1">Internal Engineer Management System</p>
                </div>

                <div class="text-center mb-6">
                    <h2 class="text-lg font-semibold">Welcome Back</h2>
                    <p class="text-sm text-white/50 mt-1">Sign in to continue to your dashboard</p>
                </div>

                <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <div class="input-glass rounded-lg flex items-center px-3 py-2.5">
                            <svg class="w-5 h-5 accent-icon mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Email address"
                                class="bg-transparent border-0 focus:ring-0 w-full text-white placeholder-white/35 p-0"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <div class="input-glass rounded-lg flex items-center px-3 py-2.5">
                            <svg class="w-5 h-5 accent-icon mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Password"
                                class="bg-transparent border-0 focus:ring-0 w-full text-white placeholder-white/35 p-0"
                            >
                            <button type="button" x-on:click="showPassword = !showPassword" class="text-white/50 hover:text-white ml-2 shrink-0">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-white/75">
                            <input type="checkbox" name="remember" class="accent-checkbox rounded border-white/30 bg-transparent">
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="accent-text hover:opacity-80">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="accent-btn w-full text-white font-semibold py-3 rounded-lg flex items-center justify-center gap-2 transition"
                    >
                        Sign In
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-white/35 mt-6">
                © {{ date('Y') }} Web App Engineer. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>