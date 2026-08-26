<header class="sticky top-0 z-30 bg-white/85 dark:bg-slate-900/85 backdrop-blur-xl border-b border-slate-200/70 h-16 sm:h-20 flex items-center justify-between px-4 sm:px-8 lg:px-8">
    <div class="flex items-center gap-3">
        {{-- Hamburger, hanya tampil di mobile --}}
        <button id="hamburgerBtn" type="button"
                class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- LOGO --}}
        <a href="{{ route('dashboard') }}" class="flex-shrink-0">
            <img src="{{ asset('images/logo/logo-lightmode.png') }}" alt="Tridaya App"
                 class="h-9 sm:h-11 w-auto object-contain bg-white rounded-lg p-0.5 dark:bg-transparent dark:hidden">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Tridaya App"
                 class="h-9 sm:h-11 w-auto object-contain bg-white rounded-lg p-0.5 hidden dark:block dark:bg-transparent">
        </a>

        {{-- TITLE --}}
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-800">
                {{ $pageTitle ?? 'Tridaya App' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 hidden sm:block">
                Full Visibility. Zero Guesswork • 3DY Group
            </p>
        </div>
    </div>
    <!-- ...sisanya tetap sama... -->

    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Dark mode toggle (switch) --}}
        <button id="darkToggle" type="button"
                class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                aria-label="Toggle Dark Mode">
            <span class="sr-only">Toggle Dark Mode</span>
            <span class="absolute inset-0 rounded-full bg-slate-300 dark:bg-slate-600 transition-colors duration-200"></span>
            <span class="relative inline-flex items-center justify-center w-5 h-5 rounded-full bg-white shadow-sm transition-transform duration-200 dark:translate-x-6">
                <svg class="w-3 h-3 text-amber-500 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                </svg>
                <svg class="w-3 h-3 text-slate-600 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </span>
        </button>

        <span class="text-sm text-slate-600">
            {{ \Illuminate\Support\Str::limit(auth()->user()->name, 16, '…') }}
        </span>

        {{-- Tampilkan role dari Spatie --}}
        @if(auth()->user()->roles->isNotEmpty())
            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                @if(in_array(auth()->user()->roles->first()->name, ['super-admin', 'admin', 'manager']))
                    bg-purple-100 text-purple-700
                @elseif(auth()->user()->roles->first()->name == 'teknisi')
                    bg-green-100 text-green-700
                @elseif(auth()->user()->roles->first()->name == 'sales')
                    bg-blue-100 text-blue-700
                @elseif(auth()->user()->roles->first()->name == 'marketing')
                    bg-orange-100 text-orange-700
                @else
                    bg-gray-100 text-gray-700
                @endif
            ">
                {{ auth()->user()->roles->first()->name }}
            </span>
        @else
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                No Role
            </span>
        @endif

        {{-- Dropdown Avatar --}}
        <div class="relative" x-data="{ open: false }">
            {{-- Avatar --}}
            <button @click="open = !open"
                    class="rounded-full hover:ring-2 hover:ring-indigo-300 transition shrink-0">
                <x-user-avatar :user="auth()->user()" size="w-10 h-10" text="text-sm" />
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50 origin-top-right">
                
                {{-- Profil --}}
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profil
                </a>

                <hr class="my-1 border-slate-200">

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </div>

</header>