<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('settings.edit') }}"
                   title="{{ __('Kembali ke Setting') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-white dark:border-slate-600 dark:text-slate-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">{{ __('Pengaturan Lanjutan') }}</h1>
                    <p class="mt-0.5 text-sm text-slate-500">{{ __('Keamanan akun Anda') }}</p>
                </div>
            </div>
        </div>

        <x-profile-tabs />

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            @include('settings.partials.update-account-form')
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm dark:border-red-900/50 dark:bg-slate-800">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
