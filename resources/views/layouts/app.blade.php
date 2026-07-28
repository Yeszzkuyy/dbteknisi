<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Web App Engineer') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @livewireStyles

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: white;
            border-right: 1px solid #e2e8f0;
            z-index: 999;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 1024px) {
            .sidebar {
                position: relative;
                transform: translateX(0) !important;
                width: 280px;
                height: 100vh;
                flex-shrink: 0;
            }
            .sidebar-overlay { display: none !important; }
            .main-content { flex: 1; min-width: 0; }
            .app-wrapper { display: flex; min-height: 100vh; }
            #hamburgerBtn { display: none !important; }
        }

        @media (max-width: 1023px) {
            .app-wrapper { display: flex; min-height: 100vh; }
            .sidebar { width: 280px; }
            .main-content { flex: 1; }
        }
    </style>
</head>

<body class="bg-slate-100 font-sans antialiased">

    {{-- Overlay --}}
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="app-wrapper">
        <div id="sidebar" class="sidebar">
            @include('layouts.partials.sidebar')
        </div>

        <div class="main-content">
            @include('layouts.partials.header')

            <div class="px-4 sm:px-6 lg:px-8 pt-5">
                @if(session('success'))
                    <div class="rounded-xl bg-green-100 border border-green-300 text-green-700 px-5 py-3 mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="rounded-xl bg-red-100 border border-red-300 text-red-700 px-5 py-3 mb-4">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-8">
                @isset($header)
                    <div class="mb-6">{{ $header }}</div>
                @endisset
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburgerBtn = document.getElementById('hamburgerBtn');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            }
            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
            function toggleSidebar() {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            }

            hamburgerBtn?.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', closeSidebar);

            // AUTO HIDE: tutup sidebar tiap kali link menu di klik (khusus mobile)
            sidebar.querySelectorAll('a, button[type="submit"]').forEach(el => {
                el.addEventListener('click', () => {
                    if (window.innerWidth < 1024) closeSidebar();
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) closeSidebar();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
            });
        });
    </script>
</body>
</html>