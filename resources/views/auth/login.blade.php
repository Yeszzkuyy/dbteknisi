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
            background:
                radial-gradient(circle at 76% 44%, rgba(129, 140, 248, 0.5), transparent 30%),
                linear-gradient(135deg, #181b68 0%, #3028b4 48%, #5750d8 100%);
        }

        .login-background-layer {
            position: absolute;
            z-index: 1;
            pointer-events: none;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
            will-change: transform;
        }

        .login-background-grid {
            top: -12%;
            right: -8%;
            bottom: -12%;
            left: 28%;
            opacity: 0.13;
            background-image:
                linear-gradient(rgba(224, 231, 255, 0.22) 1px, transparent 1px),
                linear-gradient(90deg, rgba(224, 231, 255, 0.22) 1px, transparent 1px);
            background-size: 5rem 5rem;
            animation: background-drift 34s linear infinite;
        }

        .login-background-glow {
            top: -14%;
            right: -12%;
            width: min(58vw, 52rem);
            height: min(58vw, 52rem);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2), rgba(129, 140, 248, 0.1) 38%, transparent 70%);
            filter: blur(18px);
            opacity: 0.86;
            animation: background-breathe 12s ease-in-out infinite alternate;
        }

        .login-background-dust {
            inset: 0;
            opacity: 0.38;
            background-image:
                radial-gradient(circle at 74% 24%, rgba(255, 255, 255, 0.65) 0 1px, transparent 1.5px),
                radial-gradient(circle at 87% 68%, rgba(224, 231, 255, 0.5) 0 1px, transparent 1.5px),
                radial-gradient(circle at 61% 78%, rgba(244, 114, 182, 0.46) 0 1px, transparent 1.5px);
            background-size: 11rem 13rem, 15rem 17rem, 19rem 21rem;
        }

        .login-visual-fade {
            position: absolute;
            z-index: 8;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, var(--login-paper) 0%, rgba(247, 249, 252, 0.98) 13%, rgba(247, 249, 252, 0.68) 27%, rgba(247, 249, 252, 0) 48%),
                linear-gradient(0deg, rgba(15, 23, 42, 0.16), transparent 28%, rgba(15, 23, 42, 0.08));
        }

        .login-composition {
            position: absolute;
            z-index: 6;
            top: 50%;
            left: 70%;
            width: min(58vw, 52rem);
            aspect-ratio: 1;
            pointer-events: none;
            transform: translate(-50%, -50%);
        }

        .login-composition::before,
        .login-composition::after {
            position: absolute;
            border: 1px solid rgba(224, 231, 255, 0.13);
            border-radius: 50%;
            content: '';
        }

        .login-composition::before {
            inset: 8%;
            transform: rotate(-18deg) scaleX(0.76);
        }

        .login-composition::after {
            inset: 16%;
            border-color: rgba(196, 181, 253, 0.1);
            transform: rotate(24deg) scaleY(0.72);
        }

        .login-parallax-layer {
            position: absolute;
            pointer-events: none;
            transform: translate3d(var(--parallax-x, 0px), var(--parallax-y, 0px), 0);
            transition: transform 900ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .login-connectors-parallax,
        .login-asset-orbit-parallax,
        .login-character-parallax {
            inset: 0;
        }

        .login-connectors {
            position: absolute;
            z-index: 1;
            inset: 4%;
            width: 92%;
            height: 92%;
            overflow: visible;
        }

        .connector-base,
        .connector-flow {
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .connector-base {
            stroke: rgba(224, 231, 255, 0.24);
            stroke-width: 0.55;
        }

        .connector-flow {
            stroke: #e0e7ff;
            stroke-width: 1.2;
            stroke-dasharray: 1 8;
            filter: url(#connector-glow);
            animation: connector-flow 10s linear infinite;
        }

        .connector-flow.flow-two { animation-delay: -2.2s; }
        .connector-flow.flow-three { animation-delay: -4.6s; }
        .connector-flow.flow-four { animation-delay: -6.8s; }
        .connector-flow.flow-five { animation-delay: -8.4s; }
        .connector-flow.flow-six { animation-delay: -1.1s; }

        .login-asset-orbit {
            position: absolute;
            z-index: 3;
            inset: 0;
            animation: icon-orbit 32s linear infinite;
        }

        .login-icon {
            position: absolute;
            width: 14%;
            aspect-ratio: 1;
        }

        .login-icon-frame {
            position: relative;
            display: grid;
            width: 100%;
            height: 100%;
            place-items: center;
            animation: icon-float var(--float-duration, 6s) ease-in-out var(--float-delay, 0s) infinite;
        }

        .login-icon-frame::before {
            position: absolute;
            z-index: -1;
            inset: 13%;
            border-radius: 50%;
            background: radial-gradient(circle, var(--icon-glow, rgba(165, 180, 252, 0.8)), transparent 70%);
            filter: blur(11px);
            opacity: 0.82;
            content: '';
        }

        .login-icon-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 7px var(--icon-glow, rgba(224, 231, 255, 0.65)));
        }

        .login-icon-one { top: 6%; left: 42%; --icon-glow: rgba(186, 230, 253, 0.95); --float-duration: 6.4s; --float-delay: -1.2s; }
        .login-icon-two { top: 26%; left: 12%; --icon-glow: rgba(196, 181, 253, 0.95); --float-duration: 7.2s; --float-delay: -3.4s; }
        .login-icon-three { top: 59%; left: 5%; --icon-glow: rgba(249, 168, 212, 0.92); --float-duration: 5.8s; --float-delay: -2.3s; }
        .login-icon-four { top: 20%; right: 10%; --icon-glow: rgba(253, 230, 138, 0.95); --float-duration: 6.8s; --float-delay: -4.1s; }
        .login-icon-five { top: 54%; right: 2%; --icon-glow: rgba(253, 186, 116, 0.92); --float-duration: 7.6s; --float-delay: -1.8s; }
        .login-icon-six { right: 38%; bottom: 1%; --icon-glow: rgba(252, 165, 165, 0.95); --float-duration: 6.1s; --float-delay: -4.8s; }

        .login-character-parallax {
            z-index: 5;
        }

        .login-character-float {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 43%;
            transform: translate(-50%, -50%);
            animation: character-float 8s ease-in-out infinite;
        }

        .login-character-float::before {
            position: absolute;
            z-index: -1;
            right: 7%;
            bottom: 7%;
            left: 7%;
            height: 22%;
            border-radius: 50%;
            background: rgba(129, 140, 248, 0.48);
            filter: blur(22px);
            content: '';
        }

        .login-character-float img {
            display: block;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 18px 18px rgba(23, 37, 84, 0.26));
        }

        .visual-copy {
            position: absolute;
            z-index: 9;
            right: clamp(1.5rem, 7vw, 7rem);
            bottom: clamp(2rem, 8vh, 6rem);
            width: min(22rem, 30vw);
            color: #ffffff;
            text-align: right;
        }

        .visual-copy h2 {
            margin: 0;
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

        @keyframes login-rise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes background-drift {
            from { background-position: 0 0, 0 0; }
            to { background-position: 5rem 3rem, -3rem 4rem; }
        }

        @keyframes background-breathe {
            from { opacity: 0.58; }
            to { opacity: 0.9; }
        }

        @keyframes connector-flow {
            to { stroke-dashoffset: -90; }
        }

        @keyframes icon-orbit {
            to { transform: rotate(360deg); }
        }

        @keyframes icon-float {
            0%, 100% { transform: translate3d(0, 0, 0) rotate(-2deg); }
            50% { transform: translate3d(0, -0.65rem, 0) rotate(3deg); }
        }

        @keyframes character-float {
            0%, 100% { transform: translate(-50%, -50%) rotate(-1deg); }
            50% { transform: translate(-50%, calc(-50% - 0.8rem)) rotate(1deg); }
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

            .login-background-grid {
                top: -18%;
                right: -18%;
                bottom: -18%;
                left: -18%;
                background-size: 4rem 4rem;
            }

            .login-background-glow {
                top: -10%;
                right: -28%;
                width: 22rem;
                height: 22rem;
            }

            .login-visual-fade {
                background:
                    linear-gradient(180deg, rgba(247, 249, 252, 0) 45%, rgba(247, 249, 252, 0.72) 82%, var(--login-paper) 100%),
                    linear-gradient(0deg, rgba(15, 23, 42, 0.14), transparent 45%);
            }

            .login-composition {
                top: 48%;
                left: 50%;
                width: min(92vw, 24rem);
                transform: translate(-50%, -50%);
            }

            .login-asset-orbit {
                animation-duration: 40s;
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

        }

        @media (prefers-reduced-motion: reduce) {
            .login-heading,
            .login-form {
                animation: none;
                transition: none;
            }

            .login-background-grid,
            .login-background-glow,
            .connector-flow,
            .login-asset-orbit,
            .login-character-float,
            .login-parallax-layer {
                animation: none;
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
                        [this.$refs.backgroundGrid, -2, -2],
                        [this.$refs.backgroundGlow, 6, 4],
                        [this.$refs.connectors, -7, -5],
                        [this.$refs.orbit, 12, 8],
                        [this.$refs.character, -8, -6],
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
                [this.$refs.backgroundGrid, this.$refs.backgroundGlow, this.$refs.connectors, this.$refs.orbit, this.$refs.character].forEach((element) => {
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
            <div x-ref="backgroundGrid" class="login-background-layer login-background-grid"></div>
            <div x-ref="backgroundGlow" class="login-background-layer login-background-glow"></div>
            <div class="login-background-layer login-background-dust"></div>

            <div class="login-composition">
                <div x-ref="connectors" class="login-parallax-layer login-connectors-parallax">
                    <svg class="login-connectors" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <filter id="connector-glow" x="-50%" y="-50%" width="200%" height="200%">
                                <feGaussianBlur stdDeviation="1.2" result="blur" />
                                <feMerge>
                                    <feMergeNode in="blur" />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                        </defs>

                        <path class="connector-base" d="M50 49C50 38 47 26 46 14" />
                        <path class="connector-flow flow-one" d="M50 49C50 38 47 26 46 14" />
                        <path class="connector-base" d="M46 50C36 44 27 37 18 31" />
                        <path class="connector-flow flow-two" d="M46 50C36 44 27 37 18 31" />
                        <path class="connector-base" d="M45 56C34 62 23 68 11 70" />
                        <path class="connector-flow flow-three" d="M45 56C34 62 23 68 11 70" />
                        <path class="connector-base" d="M54 48C64 42 74 35 85 27" />
                        <path class="connector-flow flow-four" d="M54 48C64 42 74 35 85 27" />
                        <path class="connector-base" d="M55 56C67 59 78 61 90 63" />
                        <path class="connector-flow flow-five" d="M55 56C67 59 78 61 90 63" />
                        <path class="connector-base" d="M49 57C47 69 47 79 45 91" />
                        <path class="connector-flow flow-six" d="M49 57C47 69 47 79 45 91" />

                        <circle cx="46" cy="14" r="1.2" fill="#BAE6FD" />
                        <circle cx="18" cy="31" r="1.2" fill="#C4B5FD" />
                        <circle cx="11" cy="70" r="1.2" fill="#F9A8D4" />
                        <circle cx="85" cy="27" r="1.2" fill="#FDE68A" />
                        <circle cx="90" cy="63" r="1.2" fill="#FDBA74" />
                        <circle cx="45" cy="91" r="1.2" fill="#FCA5A5" />
                    </svg>
                </div>

                <div x-ref="orbit" class="login-parallax-layer login-asset-orbit-parallax">
                    <div class="login-asset-orbit">
                        <div class="login-icon login-icon-one">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon1.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                        <div class="login-icon login-icon-two">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon2.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                        <div class="login-icon login-icon-three">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon3.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                        <div class="login-icon login-icon-four">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon4.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                        <div class="login-icon login-icon-five">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon5.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                        <div class="login-icon login-icon-six">
                            <div class="login-icon-frame">
                                <img src="{{ asset('images/Asset_LoginPage/icon6.svg') }}" alt="" decoding="async">
                            </div>
                        </div>
                    </div>
                </div>

                <div x-ref="character" class="login-parallax-layer login-character-parallax">
                    <div class="login-character-float">
                        <img src="{{ asset('images/Asset_LoginPage/character.svg') }}" alt="" fetchpriority="high" decoding="async">
                    </div>
                </div>
            </div>

            <div class="login-visual-fade"></div>
            <div class="visual-copy">
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

        </section>
    </main>
</body>
</html>
