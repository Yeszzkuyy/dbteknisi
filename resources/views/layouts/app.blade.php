<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tridaya App') }}</title>

    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/logo.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script>if(localStorage.getItem('dark-mode')==='true'||(!('dark-mode' in localStorage)&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.documentElement.classList.add('dark')}else{document.documentElement.classList.remove('dark')}</script>

    @livewireStyles

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--sidebar-bg:#fff;--sidebar-border:#e2e8f0;--card-bg:#fff;--card-border:#e2e8f0;--card-bg-hover:#f8fafc;--text-primary:#1e293b;--text-secondary:#64748b;--text-muted:#94a3b8;--input-bg:#f1f5f9;--input-border:#cbd5e1;--input-text:#1e293b}
        .dark{--sidebar-bg:#1e293b;--sidebar-border:#334155;--card-bg:#1e293b;--card-border:#334155;--card-bg-hover:#2d3a4e;--text-primary:#f1f5f9;--text-secondary:#cbd5e1;--text-muted:#64748b;--input-bg:#243244;--input-border:#475569;--input-text:#f1f5f9}
        .sidebar{position:fixed;top:0;left:0;height:100vh;width:280px;background:var(--sidebar-bg);border-right:1px solid var(--sidebar-border);z-index:999;transform:translateX(-100%);transition:transform .3s ease-in-out;overflow-y:auto}
        .sidebar.open{transform:translateX(0)}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:998}
        .sidebar-overlay.active{display:block}
        .main-content{margin-left:0;width:100%;min-height:100vh;display:flex;flex-direction:column}

        @media(min-width:1024px){
            .sidebar{position:fixed;left:0;top:0;transform:translateX(0)!important;width:280px;height:100vh;overflow:hidden}
            .sidebar-overlay{display:none!important}
            .main-content{margin-left:280px;width:calc(100% - 280px);flex:1;min-width:0}
            .app-wrapper{display:flex;min-height:100vh}
            #hamburgerBtn{display:none!important}
        }
        @media(max-width:1023px){
            .app-wrapper{display:flex;min-height:100vh}
            .sidebar{width:280px}
            .main-content{flex:1}
        }

        .dark .bg-white{background-color:var(--card-bg)!important}
        .dark #darkToggle .bg-white{background-color:#fff!important}
        .dark .bg-slate-50{background-color:var(--card-bg)!important}
        .dark .bg-slate-100{background-color:#243244!important}
        .dark .bg-slate-200{background-color:#334155!important}
        .dark .bg-slate-300{background-color:#475569!important}
        .dark .bg-gray-300{background-color:#475569!important}
        .dark .hover\:bg-slate-100:hover{background-color:#243244!important}
        .dark .hover\:bg-slate-200:hover{background-color:#334155!important}
        .dark .hover\:bg-slate-300:hover{background-color:#475569!important}
        .dark .hover\:bg-slate-50:hover{background-color:var(--card-bg-hover)!important}
        .dark .hover\:bg-gray-50:hover{background-color:var(--card-bg-hover)!important}
        .dark .border-slate-200{border-color:var(--card-border)!important}
        .dark .border-slate-300{border-color:var(--input-border)!important}
        .dark .text-slate-900,.dark .text-gray-800{color:var(--text-primary)!important}
        .dark .text-slate-800,.dark .text-gray-900,.dark .text-slate-700,.dark .text-gray-700{color:var(--text-primary)!important}
        .dark .text-slate-600,.dark .text-gray-600{color:var(--text-secondary)!important}
        .dark .text-slate-500,.dark .text-gray-500{color:var(--text-muted)!important}
        .dark th.text-slate-500,.dark th.text-slate-600{color:var(--text-secondary)!important}
        input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=color]):not([type=range]):not([type=hidden]),select,textarea{background-color:var(--input-bg)!important;border-color:var(--input-border)!important;color:var(--input-text)!important}
        input:focus,select:focus,textarea:focus{border-color:#3b82f6!important}
        input::placeholder,textarea::placeholder{color:var(--text-muted)!important}
        .dark input[type=file]{color-scheme:dark}
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 font-sans antialiased">

    <div id="sidebarOverlay" class="sidebar-overlay dark:bg-black/60"></div>

    <div class="app-wrapper">
        <div id="sidebar" class="sidebar">
            @include('layouts.partials.sidebar')
        </div>

        <div class="main-content">
            @include('layouts.partials.header')
            @include('partials.avatar-lightbox')

            <div class="px-4 sm:px-6 lg:px-8 pt-5">
                @if(session('success'))
                    <div class="rounded-xl bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 px-5 py-3 mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="rounded-xl bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 px-5 py-3 mb-4">{{ session('error') }}</div>
                @endif
            </div>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-8">
                @isset($header)<div class="mb-6">{{ $header }}</div>@endisset
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded',function(){
            var s=document.getElementById('sidebar'),o=document.getElementById('sidebarOverlay'),h=document.getElementById('hamburgerBtn');
            function open(){s.classList.add('open');o.classList.add('active')}
            function close(){s.classList.remove('open');o.classList.remove('active')}
            h&&h.addEventListener('click',function(){s.classList.contains('open')?close():open()});
            o.addEventListener('click',close);
            s.querySelectorAll('a,button[type="submit"]').forEach(function(e){e.addEventListener('click',function(){window.innerWidth<1024&&close()})});
            window.addEventListener('resize',function(){window.innerWidth>=1024&&close()});
            document.addEventListener('keydown',function(e){e.key==='Escape'&&s.classList.contains('open')&&close()});
            var t=document.getElementById('darkToggle');
            t&&t.addEventListener('click',function(){var d=document.documentElement.classList.toggle('dark');localStorage.setItem('dark-mode',d)});
            // Reveal saat scroll — hormati prefers-reduced-motion
            if(!window.matchMedia('(prefers-reduced-motion: reduce)').matches&&'IntersectionObserver' in window){
                var io=new IntersectionObserver(function(entries){
                    entries.forEach(function(en){if(en.isIntersecting){en.target.classList.add('in-view');io.unobserve(en.target)}});
                },{threshold:0.12});
                document.querySelectorAll('[data-reveal]').forEach(function(el){io.observe(el)});
            }else{
                document.querySelectorAll('[data-reveal]').forEach(function(el){el.classList.add('in-view')});
            }
        });
    </script>
</body>
</html>