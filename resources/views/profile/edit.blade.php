<x-app-layout>
    <div class="w-full space-y-6 tab-container">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">{{ __('Profil') }}</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">{{ __('Informasi akun Anda') }}</p>
            </div>
            <x-icon-button as="a" icon="back" href="{{ route('dashboard') }}" title="{{ __('Kembali') }}" />
        </div>

        <x-profile-tabs />

        {{-- Kartu profil --}}
        <section class="rounded-2xl border border-slate-200/60 bg-white p-10 text-center shadow-lg sm:p-14 dark:border-slate-700/50 dark:bg-slate-800 dark:shadow-black/20">
                @php($secure = $user->hasSecurePassword())
                @php($isYeski = $user->hasAnimatedAvatarBorder())
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                      x-data="{ preview: @js($user->avatar ? asset('storage/' . $user->avatar) : null) }">
                    @csrf

                    {{-- Foto profil — klik untuk mengganti --}}
                    <button type="button" @click="$refs.avatar.click()"
                            class="group relative mx-auto block cursor-pointer rounded-full transition duration-200 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800"
                            aria-label="{{ __('Ubah foto profil') }}">
                        {{-- Border running khusus Yeski: dua warna tema saling mengejar --}}
                        @if($isYeski)
                            <span aria-hidden="true" title="{{ __('Founder') }}"
                                  class="absolute -inset-1.5 rounded-full animate-spin [animation-duration:2.5s] motion-reduce:animate-none bg-[conic-gradient(from_0deg,rgb(var(--accent-500))_0deg,transparent_100deg,rgb(var(--accent-300))_180deg,transparent_280deg,rgb(var(--accent-500))_360deg)] shadow-[0_0_12px_2px_rgb(var(--accent-500)/0.45)]"></span>
                        @endif
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Foto profil') }}"
                             class="relative h-24 w-24 rounded-full object-cover ring-4 shadow-lg lg:h-28 lg:w-28 {{ $isYeski ? 'ring-slate-900/80' : ($secure ? 'ring-accent-300 dark:ring-accent-700' : 'ring-red-300 dark:ring-red-800') }}">
                        <div x-show="!preview" x-cloak aria-hidden="true"
                             class="relative flex h-24 w-24 items-center justify-center rounded-full ring-4 shadow-lg lg:h-28 lg:w-28 {{ $isYeski ? 'bg-slate-900 ring-slate-900/80' : ($secure ? 'bg-accent-100 ring-accent-300 dark:bg-accent-900/40 dark:ring-accent-700' : 'bg-red-100 ring-red-300 dark:bg-red-900/40 dark:ring-red-800') }}">
                            <span class="text-3xl font-bold lg:text-4xl {{ $isYeski ? 'text-amber-300' : ($secure ? 'text-accent-600 dark:text-accent-300' : 'text-red-600 dark:text-red-300') }}">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>

                        {{-- Overlay hover: gelap + ikon kamera --}}
                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-visible:opacity-100">
                            <x-icon name="camera" class="h-6 w-6" />
                        </span>
                        {{-- Badge kamera, selalu terlihat --}}
                        <span class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-accent-600 text-white shadow-lg ring-2 ring-white dark:ring-slate-800">
                            <x-icon name="camera" class="h-4 w-4" />
                        </span>
                    </button>

                    <input type="file" x-ref="avatar" name="avatar" class="hidden"
                           accept="image/png,image/jpeg,image/webp,image/gif"
                           x-on:change="preview = URL.createObjectURL($event.target.files[0]); $el.form.submit()">
                </form>

                {{-- Nama, role, email --}}
                <div class="mt-6 space-y-3">
                    <h2 class="text-xl font-extrabold tracking-tight text-slate-800 sm:text-2xl dark:text-white">{{ $user->name }}</h2>
                    <div>
                        <span class="inline-flex items-center rounded-full bg-accent-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-accent-700 dark:bg-accent-500/10 dark:text-accent-400">
                            {{ $user->roles->first()?->name ?? __('Tanpa Role') }}
                        </span>
                    </div>
                    <div>
                        @if($secure)
                            <span class="inline-flex items-center rounded-full bg-accent-100 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-accent-700 dark:bg-accent-900/40 dark:text-accent-300">
                                {{ __('Akun aman') }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                {{ __('Password belum diganti') }}
                            </span>
                        @endif
                    </div>
                    <p class="flex items-center justify-center gap-1.5 pt-1 text-sm text-slate-500 dark:text-slate-400">
                        <x-icon name="mail" class="h-4 w-4" />
                        {{ $user->email }}
                    </p>
                </div>

                <p class="mt-6 text-xs text-slate-400 dark:text-slate-500">{{ __('Klik foto untuk mengganti foto profil') }}</p>

                <x-input-error class="mt-3" :messages="$errors->get('avatar')" />
            </section>
    </div>
</x-app-layout>