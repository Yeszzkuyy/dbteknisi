<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               title="{{ __('Kembali ke dashboard') }}"
               class="group inline-flex shrink-0 items-center gap-1.5 rounded-xl p-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100">
                <svg class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali') }}
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl dark:text-white">{{ __('Profil') }}</h1>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ __('Informasi akun Anda') }}</p>
            </div>
        </div>

        <x-profile-tabs />

        {{-- Kartu profil --}}
        <section class="rounded-2xl border border-slate-200/60 bg-white p-10 text-center shadow-lg sm:p-14 dark:border-slate-700/50 dark:bg-slate-800 dark:shadow-black/20">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                      x-data="{ preview: @js($user->avatar ? asset('storage/' . $user->avatar) : null) }">
                    @csrf

                    {{-- Foto profil — klik untuk mengganti --}}
                    <button type="button" @click="$refs.avatar.click()"
                            class="group relative mx-auto block cursor-pointer rounded-full transition duration-200 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800"
                            aria-label="{{ __('Ubah foto profil') }}">
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Foto profil') }}"
                             class="h-24 w-24 rounded-full object-cover ring-4 ring-blue-100 shadow-lg dark:ring-blue-900/50 lg:h-28 lg:w-28">
                        <div x-show="!preview" x-cloak aria-hidden="true"
                             class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-100 ring-4 ring-blue-100 shadow-lg lg:h-28 lg:w-28 dark:bg-blue-900/40 dark:ring-blue-900/50">
                            <span class="text-3xl font-bold text-blue-600 lg:text-4xl dark:text-blue-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>

                        {{-- Overlay hover: gelap + ikon kamera --}}
                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-visible:opacity-100">
                            <x-icon name="camera" class="h-6 w-6" />
                        </span>
                        {{-- Badge kamera, selalu terlihat --}}
                        <span class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg ring-2 ring-white dark:ring-slate-800">
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
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                            {{ $user->roles->first()?->name ?? __('Tanpa Role') }}
                        </span>
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