<x-app-layout>
    @php
        $currentTheme = $user->preference('theme', 'system');
        $currentLocale = $user->preference('locale', 'id');
        $notifyEmail = (bool) $user->preference('notify_email', false);
        $notifySystem = (bool) $user->preference('notify_system', true);

        $themeOptions = [
            ['value' => 'light', 'label' => __('Terang'), 'icon' => 'sun'],
            ['value' => 'dark', 'label' => __('Gelap'), 'icon' => 'moon'],
            ['value' => 'system', 'label' => __('Sistem'), 'icon' => 'settings'],
        ];
    @endphp

    <div class="mx-auto max-w-4xl space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                   title="{{ __('Kembali ke dashboard') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-white dark:border-slate-600 dark:text-slate-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">{{ __('Setting') }}</h1>
                    <p class="mt-0.5 text-sm text-slate-500">{{ __('Preferensi aplikasi Anda') }}</p>
                </div>
            </div>
        </div>

        <x-profile-tabs />

        @if (session('status') === 'settings-updated')
            <div class="rounded-xl border border-green-300 bg-green-100 px-5 py-3 text-sm text-green-700 dark:border-green-700 dark:bg-green-900/30 dark:text-green-400">
                {{ __('Pengaturan berhasil disimpan.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            {{-- Tampilan --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800"
                     x-data="{
                        theme: '{{ $currentTheme }}',
                        apply(t) {
                            this.theme = t;
                            const root = document.documentElement;
                            if (t === 'dark') { root.classList.add('dark'); localStorage.setItem('dark-mode', 'true'); }
                            else if (t === 'light') { root.classList.remove('dark'); localStorage.setItem('dark-mode', 'false'); }
                            else {
                                localStorage.removeItem('dark-mode');
                                root.classList.toggle('dark', window.matchMedia('(prefers-color-scheme: dark)').matches);
                            }
                        }
                     }">
                <header>
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Tampilan') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Pilih tema awal aplikasi saat Anda masuk.') }}</p>
                </header>

                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach($themeOptions as $option)
                        <label class="cursor-pointer">
                            <input type="radio" name="theme" value="{{ $option['value'] }}" class="peer sr-only"
                                   x-model="theme" @change="apply('{{ $option['value'] }}')"
                                   @checked($currentTheme === $option['value'])>
                            <span class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-5 text-sm font-semibold text-slate-600 transition peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300 dark:peer-checked:border-blue-400 dark:peer-checked:bg-blue-900/30 dark:peer-checked:text-blue-300">
                                <x-icon name="{{ $option['icon'] }}" class="h-5 w-5" />
                                {{ $option['label'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('theme')" />
            </section>

            {{-- Notifikasi --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                <header>
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Notifikasi') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Atur bagaimana Anda menerima pemberitahuan.') }}</p>
                </header>

                <div class="mt-5 divide-y divide-slate-100 dark:divide-slate-700">
                    <label class="flex cursor-pointer items-center justify-between gap-4 py-4 first:pt-0">
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Notifikasi Email') }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">{{ __('Kirim pemberitahuan lead baru ke email Anda.') }}</span>
                        </span>
                        <span class="relative inline-flex shrink-0 items-center">
                            <input type="checkbox" name="notify_email" value="1" class="peer sr-only" @checked($notifyEmail)>
                            <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-green-500 dark:bg-slate-600"></span>
                            <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center justify-between gap-4 py-4 last:pb-0">
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Notifikasi Sistem') }}</span>
                            <span class="mt-0.5 block text-xs text-slate-500">{{ __('Tampilkan notifikasi di dalam aplikasi.') }}</span>
                        </span>
                        <span class="relative inline-flex shrink-0 items-center">
                            <input type="checkbox" name="notify_system" value="1" class="peer sr-only" @checked($notifySystem)>
                            <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-green-500 dark:bg-slate-600"></span>
                            <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                        </span>
                    </label>
                </div>
            </section>

            {{-- Bahasa --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
                <header>
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Bahasa') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Pilih bahasa tampilan aplikasi.') }}</p>
                </header>

                <div class="mt-5 max-w-xs">
                    <x-input-label for="locale" :value="__('Bahasa')" />
                    <select id="locale" name="locale"
                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="id" @selected($currentLocale === 'id')>Indonesia</option>
                        <option value="en" @selected($currentLocale === 'en')>English</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('locale')" />
                </div>
            </section>

            {{-- Simpan --}}
            <div class="flex items-center gap-4">
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    {{ __('Simpan Pengaturan') }}
                </button>
            </div>
        </form>

        {{-- Area lanjutan (digerbang password) --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            <header>
                <h2 class="text-lg font-bold text-slate-800">{{ __('Lanjutan') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Ganti password dan hapus akun. Area ini dilindungi konfirmasi password.') }}</p>
            </header>

            <a href="{{ route('settings.advanced') }}"
               class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-5 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                <x-icon name="settings" class="h-4 w-4" />
                {{ __('Buka Pengaturan Lanjutan') }}
            </a>
        </section>
    </div>
</x-app-layout>
