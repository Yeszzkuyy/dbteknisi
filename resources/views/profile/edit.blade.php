<x-app-layout>
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
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">{{ __('Profil') }}</h1>
                    <p class="mt-0.5 text-sm text-slate-500">{{ __('Informasi akun Anda') }}</p>
                </div>
            </div>
        </div>

        <x-profile-tabs />

        {{-- Kartu identitas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            <div class="flex flex-col items-center gap-6 text-center sm:flex-row sm:items-center sm:text-left">
                <x-user-avatar :user="$user" size="w-20 h-20" text="text-2xl" :clickable="false" />

                <div class="min-w-0 space-y-1">
                    <h2 class="truncate text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                    <div>
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $user->roles->first()?->name ?? __('Tanpa Role') }}
                        </span>
                    </div>
                    <p class="flex items-center justify-center gap-1.5 text-sm text-slate-500 sm:justify-start">
                        <x-icon name="mail" class="h-4 w-4" />
                        {{ $user->email }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Form edit informasi --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-600 dark:bg-slate-800">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>
</x-app-layout>
