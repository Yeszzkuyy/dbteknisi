<header class="bg-white border-b border-slate-200 h-16 sm:h-20 flex items-center justify-between px-4 sm:px-8 lg:px-8">
    <div class="flex items-center gap-3">
        {{-- Hamburger, hanya tampil di mobile --}}
        <button id="hamburgerBtn" type="button"
                class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- TITLE --}}
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-800">
                {{ $pageTitle ?? 'Web App Engineer' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 hidden sm:block">
                Internal Engineer Management • 3DY Group
            </p>
        </div>
    </div>
    <!-- ...sisanya tetap sama... -->

    <div class="flex items-center gap-2 sm:gap-3">

        <span class="text-sm text-slate-600">
            {{ auth()->user()->name }}
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
                    class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold text-sm hover:ring-2 hover:ring-indigo-300 transition">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                
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