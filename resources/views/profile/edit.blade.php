<x-app-layout>
    @php
        $nameParts = explode(' ', trim($user->name));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $phone = $user->preference('phone');
        $address = $user->preference('address');
    @endphp

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

        {{-- Grid: Ringkasan (kiri) + Detail (kanan) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Kiri: Ringkasan Profil --}}
            <section class="rounded-2xl border border-slate-200/60 bg-white p-6 text-center shadow-lg dark:border-slate-700/50 dark:bg-slate-800 dark:shadow-black/20">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                      x-data="{ preview: @js($user->avatar ? asset('storage/' . $user->avatar) : null) }">
                    @csrf

                    {{-- Foto profil — klik untuk mengganti --}}
                    <button type="button" @click="$refs.avatar.click()"
                            class="group relative mx-auto block cursor-pointer rounded-full transition duration-200 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800"
                            aria-label="{{ __('Ubah foto profil') }}">
                        <img x-show="preview" x-cloak :src="preview" alt="{{ __('Foto profil') }}"
                             class="h-16 w-16 rounded-full object-cover ring-2 ring-blue-100 shadow dark:ring-blue-900/50 lg:h-20 lg:w-20">
                        <div x-show="!preview" x-cloak aria-hidden="true"
                             class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 ring-2 ring-blue-100 shadow lg:h-20 lg:w-20 dark:bg-blue-900/40 dark:ring-blue-900/50">
                            <span class="text-xl font-bold text-blue-600 lg:text-2xl dark:text-blue-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>

                        {{-- Overlay hover: gelap + ikon kamera --}}
                        <span class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50 text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-visible:opacity-100">
                            <x-icon name="camera" class="h-4 w-4" />
                        </span>
                        {{-- Badge kamera, selalu terlihat --}}
                        <span class="absolute bottom-0 right-0 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white shadow ring-2 ring-white dark:ring-slate-800">
                            <x-icon name="camera" class="h-3 w-3" />
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

            {{-- Kanan: Detail Informasi --}}
            <section class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-lg sm:p-8 lg:col-span-2 dark:border-slate-700/50 dark:bg-slate-800 dark:shadow-black/20">
                <header>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('Detail Informasi') }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Data akun Anda saat ini.') }}</p>
                </header>

                <form class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="first_name" :value="__('Nama Depan')" />
                            <input id="first_name" type="text" value="{{ $firstName ?: '—' }}" disabled
                                   class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-sm disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-600 dark:bg-slate-900/40">
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('Nama Belakang')" />
                            <input id="last_name" type="text" value="{{ $lastName ?: '—' }}" disabled
                                   class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-sm disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-600 dark:bg-slate-900/40">
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Nomor HP')" />
                            <input id="phone" type="text" value="{{ $phone ?: '—' }}" disabled
                                   class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-sm disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-600 dark:bg-slate-900/40">
                        </div>
                        <div>
                            <x-input-label for="address" :value="__('Alamat')" />
                            <input id="address" type="text" value="{{ $address ?: '—' }}" disabled
                                   class="mt-1 block w-full rounded-lg border-slate-300 bg-slate-50 text-sm disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-600 dark:bg-slate-900/40">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" disabled
                                class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ __('Simpan Perubahan') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>